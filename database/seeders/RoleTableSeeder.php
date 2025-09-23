<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Menjalankan seeder untuk membuat role aplikasi Siklus.
     *
     * @return void
     */
    public function run()
    {
        // Reset cache permission agar tidak terjadi error saat seeding ulang
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar role yang akan kita gunakan di aplikasi
        $roles = [
            [
                'name' => 'admin',
                'title' => 'Admin',
                'status' => 1,
                'permissions' => [] // Kita akan definisikan permission nanti
            ],
            [
                'name' => 'koorUser',
                'title' => 'Koordinator User',
                'status' => 1,
                'permissions' => []
            ],
            [
                'name' => 'user',
                'title' => 'User (Kader)', // Memberi keterangan agar jelas
                'status' => 1,
                'permissions' => []
            ],
            [
                'name' => 'dokter',
                'title' => 'Dokter',
                'status' => 1,
                'permissions' => []
            ]
        ];

        // Loop untuk membuat setiap role
        foreach ($roles as $value) {
            // Pisahkan data permission untuk sementara
            $permission = $value['permissions'];
            unset($value['permissions']);
            
            // Buat role baru
            $role = Role::create($value);
            
            // Berikan permission jika ada (untuk sekarang kosong)
            if (!empty($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
