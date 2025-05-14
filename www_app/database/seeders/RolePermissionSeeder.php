<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
// use App\Models\PetOwner;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clearing all roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::query()->delete();
        Role::query()->delete();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();

        // Create a permission with a rule (e.g., "petOwner")
        // In Laravel, "rules" need to be implemented separately (e.g., via Gate or Policy)
        // Here, we will simply create the permission
        $petOwner = Permission::firstOrCreate([
            'name' => 'petOwner',
            'guard_name' => 'web',
        ]);

        // Creating roles
        $roleUser = Role::firstOrCreate([
            'name' => 'roleUser',
            'guard_name' => 'web',
        ]);

        $roleAdmin = Role::firstOrCreate([
            'name' => 'roleAdmin',
            'guard_name' => 'web',
        ]);

        $roleSuperadmin = Role::firstOrCreate([
            'name' => 'roleSuperadmin',
            'guard_name' => 'web',
        ]);

        // Assign the "petOwner" permission to the "roleUser" role
        $roleUser->givePermissionTo($petOwner);

        // In Laravel, there is no direct role inheritance, but permissions can be copied
        // Admin inherits permissions from User
        $roleAdmin->syncPermissions($roleUser->permissions);

        // Superadmin inherits permissions from Admin (and consequently User)
        $roleSuperadmin->syncPermissions($roleAdmin->permissions);

        // If necessary — assigning roles to users
        User::find(1)?->assignRole('roleSuperadmin');
        User::find(2)?->assignRole('roleAdmin');
        User::find(3)?->assignRole('roleUser');
    }
}
