<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
// use App\Models\PetOwner;
// use App\Models\User;
use App\Rules\PetOwnerGate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping of model policies (if you are using policies).
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Example of policy mapping:
        // Pet::class => \App\Policies\PetPolicy::class,
    ];

    /**
     * Register any policies or Gates.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Permission to access the pet controller
        // Gate::define('access-pets', function (User $user) {
        //     return $user->hasAnyRole(['roleUser', 'roleAdmin', 'roleSuperadmin']);
        // });

        // Gate to check the pet owner
        Gate::define('petOwner', [PetOwnerGate::class, 'handle']);

        // Examples of additional roles/permissions (if needed)
        // Gate::define('edit-article', function (User $user) {
        //     return $user->hasPermissionTo('edit articles');
        // });
    }
}
