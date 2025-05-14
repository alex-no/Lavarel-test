<?php

namespace App\Gates;

use App\Models\User;
use App\Models\PetOwner;

class PetOwnerGate
{
    public function handle(User $user, PetOwner $petOwner): bool
    {
        // If the role is admin or superadmin, access is granted
        if ($user->hasAnyRole(['roleAdmin', 'roleSuperadmin'])) {
            return true;
        }

        // If the role is user, check the owner
        if ($user->hasRole('roleUser') && $petOwner->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
