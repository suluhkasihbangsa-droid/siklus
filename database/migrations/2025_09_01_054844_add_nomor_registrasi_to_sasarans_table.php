<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomorRegistrasiToSasaransTable extends Migration
{
    public function up()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            $table->string('nomor_registrasi')->nullable()->unique()->after('id');
            $table->index('nomor_registrasi'); // Untuk performa query
        });
    }

    public function down()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            $table->dropIndex(['nomor_registrasi']);
            $table->dropColumn('nomor_registrasi');
        });
    }
}