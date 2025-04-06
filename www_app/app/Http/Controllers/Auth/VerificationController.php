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

        // Check if the request has a valid signature
        if (!URL::hasValidSignature($request)) {
            // If the link is expired

            // Initialize the user by email (try to find them)
            $user = User::where('email', $request->query('email'))->first();

            // If the user is found
            if ($user) {
                // If the email is not verified
                if (is_null($user->email_verified_at)) {
                    $verifyUrl = $this->generateVerifyUrl($user);
                    
                    // Send an email with a new link
                    Mail::to($user->email)->send(new ConfirmMail($user->name, $verifyUrl));

                    // Message about sending a new email
                    return response()->json([
                        'message' => $messages['link_expired'] . ' ' . $messages['new_email_sent'],
                        'user_found' => true
                    ], 403);
                } else {
                    // If the email is already verified
                    return response()->json([
                        'message' => $messages['email_verified'],
                        'user_found' => true
                    ], 200);
                }
            }
            // If the user is not found
            return response()->json([
                'message' => $messages['link_expired'] . ' ' . $messages['user_not_found'],
                'user_found' => false
            ], 404);
        }

        $user = User::find($request->query('id'));

        // Check if the user exists and the email matches
        if (!$user || $user->email !== $request->query('email')) {
            return response()->json(['message' => $messages['user_not_found_or_email_mismatch']], 404);
        }

        // Check if the email is already verified
        if ($user->email_verified_at) {
            return response()->json(['message' => $messages['email_verified']], 200);
        }

        // Update the user's email_verified_at timestamp
        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json(['message' => $messages['email_verification_success']], 200);
    }
}
