<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressForeignKeysToSasaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            $table->foreign('provinsi_id')->references('id')->on('provinsis');
            $table->foreign('kota_id')->references('id')->on('kotas');
            $table->foreign('kecamatan_id')->references('id')->on('kecamatans');
            $table->foreign('kelurahan_id')->references('id')->on('kelurahans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kota_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['kelurahan_id']);
        });
    }
}
