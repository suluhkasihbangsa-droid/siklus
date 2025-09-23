<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarnaBadgeToAturanInterpretasiTable extends Migration
{
    // Dalam file migration
    public function up()
    {
        Schema::table('aturan_interpretasi', function (Blueprint $table) {
            $table->string('warna_badge', 20)->default('secondary')->after('kode_interpretasi');
        });
    }

    public function down()
    {
        Schema::table('aturan_interpretasi', function (Blueprint $table) {
            $table->dropColumn('warna_badge');
        });
    }
}
