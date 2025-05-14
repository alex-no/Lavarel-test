<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use Illuminate\Support\Facades\Gate;
// use App\Gates\PetOwnerGate;
use App\Models\PetOwner;
use App\Policies\PetOwnerPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping of model policies (if you are using policies).
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PetOwner::class => PetOwnerPolicy::class,
    ];

    /**
     * Register any policies or Gates.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate to check the pet owner
        // Gate::define('petOwner', [PetOwnerGate::class, 'handle']);
    }
}
