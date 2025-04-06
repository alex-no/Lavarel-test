<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

trait Verifiable
{
    protected function generateVerifyUrl($user)
    {
        return URL::temporarySignedRoute(
            'email.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'email' => $user->email]
        );
    }
}
