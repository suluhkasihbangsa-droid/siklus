<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganisasiIdToSasaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            // Tambahkan kolom setelah kolom 'no_hp'
            $table->foreignId('organisasi_id')->nullable()->after('no_hp')->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            $table->dropForeign(['organisasi_id']);
            $table->dropColumn('organisasi_id');
        });
    }
}
