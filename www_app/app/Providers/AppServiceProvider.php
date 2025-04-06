<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\MessageHelper;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191); // For compatibility with MySQL
        Model::unguard(); // Disables mass assignment (if needed)
        
        // Disable automatic pluralization
        Model::preventAccessingMissingAttributes(false);

        App::macro('getMessages', function (array $keys, ?string $locale = null) {
            return MessageHelper::getMessages($keys, $locale);
        });
   
    }
}
