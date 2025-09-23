<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStrAndSipToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kode untuk MENAMBAHKAN kolom baru
            $table->string('nomor_str')->nullable()->after('status')->comment('Hanya untuk user tipe dokter');
            $table->string('nomor_sip')->nullable()->after('nomor_str')->comment('Hanya untuk user tipe dokter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kode untuk MENGHAPUS kolom jika di-rollback
            $table->dropColumn(['nomor_str', 'nomor_sip']);
        });
    }
}