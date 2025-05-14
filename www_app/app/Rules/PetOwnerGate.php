<?php

namespace App\Rules;

use App\Models\User;
use App\Models\PetOwner;

class PetOwnerGate
{
    public function handle(User $user, PetOwner $pet): bool
    {
        return $pet->owner_id === $user->id;
    }
}
