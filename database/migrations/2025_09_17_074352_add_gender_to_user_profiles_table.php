<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderToUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Menambahkan kolom 'gender' dengan tipe char(1)
            // 'L' untuk Laki-laki, 'P' untuk Perempuan
            // Dibuat nullable() agar data lama tidak error
            // diletakkan setelah kolom 'user_id' agar rapi
            $table->char('gender', 1)->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('gender');
        });
    }
}