<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // TAMBAHKAN INI

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'koorUser']);
        Role::create(['name' => 'user']); // Ini adalah Kader
        Role::create(['name' => 'dokter']);
        
        // JIKA INGIN MENAMBAHKAN ROLE BARU, TAMBAHKAN DI SINI
        // Role::create(['name' => 'nama_role_baru']);
    }
}