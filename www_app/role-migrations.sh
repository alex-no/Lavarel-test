#!/bin/bash
composer update
composer require spatie/laravel-permission

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# php artisan make:seeder RolePermissionSeeder

php artisan db:seed --class=RolePermissionSeeder

# php artisan make:rule PetOwnerRule
# php artisan make:policy PetOwnerPolicy --model=PetOwner