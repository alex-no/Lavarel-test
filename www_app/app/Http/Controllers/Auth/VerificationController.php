<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    /**
     * User email verification
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        $language = App::getLocale();
        if ($request->user()->hasVerifiedEmail()) {
            $messages = [
                'uk' => 'Email вже підтверджено.',
                'en' => 'Email already verified.',
                'ru' => 'Email уже подтвержден.'
            ];
            return response()->json(['message' => $messages[$language] ?? $messages['uk']], 200);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $messages = [
            'uk' => 'Email успішно підтверджено!',
            'en' => 'Email successfully verified!',
            'ru' => 'Email успешно подтвержден!'
        ];
        return response()->json(['message' => $messages[$language] ?? $messages['uk']], 200);
    }
}
