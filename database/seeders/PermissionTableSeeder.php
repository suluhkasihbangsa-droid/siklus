<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'role.create',
            'role.edit',
            'role.delete',
            'role.view',
            'permission.create',
            'permission.edit', 
            'permission.delete',
            'permission.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.view',
            'sasaran.create',
            'sasaran.edit',
            'sasaran.delete',
            'sasaran.view',
            'organisasi.create',
            'organisasi.edit',
            'organisasi.delete',
            'organisasi.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}