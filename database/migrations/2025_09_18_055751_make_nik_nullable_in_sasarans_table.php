<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNikNullableInSasaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            // Ubah kolom nik untuk mengizinkan nilai NULL
            $table->string('nik', 16)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            // Kembalikan seperti semula jika di-rollback
            $table->string('nik', 16)->nullable(false)->change();
        });
    }
}
