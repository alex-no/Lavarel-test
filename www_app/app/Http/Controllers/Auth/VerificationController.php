<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Verifiable;
use Illuminate\Http\Request;
//use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use Carbon\Carbon;

class VerificationController extends Controller
{
    use Verifiable;

/**
 * @OA\Get(
 *     path="/verify-email",
 *     summary="Verify email address",
 *     tags={"Authentication"},
 *     description="Verifies the user's email address using a signed URL with email, expiration timestamp, user ID, and signature.",
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=true,
 *         description="User's email address (URL-encoded)",
 *         @OA\Schema(type="string", format="email", example="alex@4n.com.ua")
 *     ),
 *     @OA\Parameter(
 *         name="expires",
 *         in="query",
 *         required=true,
 *         description="Expiration timestamp of the signed URL",
 *         @OA\Schema(type="integer", example=1743993783)
 *     ),
 *     @OA\Parameter(
 *         name="signature",
 *         in="query",
 *         required=true,
 *         description="Signature for verifying the URL",
 *         @OA\Schema(type="string", example="464922ad1874fe858f0de7bc906f992fb6c19465b740dbf240b76a99c1d43072")
 *     ),
 *     @OA\Parameter(
 *         name="lang",
 *         in="query",
 *         required=false,
 *         description="Optional language code (e.g., 'en', 'uk')",
 *         @OA\Schema(type="string", example="en")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email verified successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or expired verification link"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Email already verified or user not found"
 *     )
 * )
 */
//    public function verify(EmailVerificationRequest $request)
    public function verify(Request $request)
    {
        $messages = App::getMessages([
            'link_expired',
            'new_email_sent',
            'email_verified',
            'user_not_found',
            'user_not_found_or_email_mismatch',
            'email_verification_success',
        ]);

        // Check if the request expects a JSON response
        $wantsJson = $request->expectsJson();

        // Check if the request has a valid signature
        if (!URL::hasValidSignature($request)) {
            // Initialize the user by email (try to find them)
            $user = User::where('email', $request->query('email'))->first();

            // If the user is found
            if ($user) {
                // If the email is not verified
                if (is_null($user->email_verified_at)) {
                    // Send an email with a new link
                    $verifyUrl = $this->generateVerifyUrl($user);
                    Mail::to($user->email)->send(new ConfirmMail($user->name, $verifyUrl));

                    // Message about sending a new email
                    $message = $messages['link_expired'] . ' ' . $messages['new_email_sent'];
                    return $this->respond($message, $wantsJson, 403, true);
                }

                // If the email is already verified
                return $this->respond($messages['email_verified'], $wantsJson, 200, true);
            }

            // If the user is not found
            $message = $messages['link_expired'] . ' ' . $messages['user_not_found'];
            return $this->respond($message, $wantsJson, 404, false);
        }

        $user = User::find($request->query('id'));

        // Check if the user exists and the email matches
        if (!$user || $user->email !== $request->query('email')) {
            return $this->respond($messages['user_not_found_or_email_mismatch'], $wantsJson, 404);
        }

        // Check if the email is already verified
        if ($user->email_verified_at) {
            return $this->respond($messages['email_verified'], $wantsJson, 200);
        }

        // Update the user's email_verified_at timestamp
        $user->email_verified_at = Carbon::now();
        $user->save();

        return $this->respond($messages['email_verification_success'], $wantsJson, 200);
    }

    protected function respond(string $message, bool $wantsJson, int $statusCode, ?bool $userFound = null)
    {
        if ($wantsJson) {
            $response = ['message' => $message];
            if (!is_null($userFound)) {
                $response['user_found'] = $userFound;
            }
            return response()->json($response, $statusCode);
        }

        $language = App::getLocale();
        return response()->view("verify.$language", ['message' => $message], $statusCode);
    }
}
