<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan migration permission sudah dijalankan sebelum seeding
        $this->call([
            RoleSeeder::class,            // Seeder untuk membuat roles DULU
            //PermissionTableSeeder::class, // Baru kemudian permissions
            AlamatSeeder::class,
            AturanInterpretasiSeeder::class,
            ObatSeeder::class,
        ]);
        
        \App\Models\User::factory(40)->create()->each(function($user) {
            $user->assignRole('user');
        });
        \App\Models\UserProfile::factory(43)->create();
    }
}