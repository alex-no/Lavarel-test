<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    protected function authorizeRole(array $roles = ['roleAdmin', 'roleSuperadmin']): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'You do not have permission to access this information.');
        }
    }
}
