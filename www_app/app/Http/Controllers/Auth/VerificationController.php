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
     *     path="/api/email/verify/{id}/{email}",
     *     summary="Verify user email address",
     *     tags={"Authentication"},
     *     description="Verify the user's email address",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="Email for verification",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="signature",
     *         in="path",
     *         required=true,
     *         description="Signature for verification",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid or expired link"
     *     ),
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
