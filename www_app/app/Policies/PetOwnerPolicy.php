<?php

namespace App\Policies;

use App\Models\PetOwner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PetOwnerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PetOwner $petOwner): bool
    {
        // Admins and superadmins can access any record
        // Users can only access their own records
        return $user->hasAnyRole(['roleAdmin', 'roleSuperadmin'])
            || ($user->hasRole('roleUser') && $petOwner->user_id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PetOwner $petOwner): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PetOwner $petOwner): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PetOwner $petOwner): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PetOwner $petOwner): bool
    {
        return false;
    }
}
