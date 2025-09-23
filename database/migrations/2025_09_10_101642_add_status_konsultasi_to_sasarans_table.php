<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusKonsultasiToSasaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            // Kolom untuk status, defaultnya 'Tersedia'
            $table->string('status_konsultasi')->default('Tersedia')->after('updated_at');

            // Kolom untuk menyimpan ID dokter yang sedang berkonsultasi
            $table->foreignId('konsultasi_oleh_id')->nullable()->after('status_konsultasi')->constrained('users')->nullOnDelete();

            // Kolom untuk menyimpan waktu mulai konsultasi
            $table->timestamp('konsultasi_dimulai_pada')->nullable()->after('konsultasi_oleh_id');
        });
    }

    public function down()
    {
        Schema::table('sasarans', function (Blueprint $table) {
            // Ini untuk proses rollback, jika kita perlu membatalkan migrasi
            $table->dropForeign(['konsultasi_oleh_id']);
            $table->dropColumn(['status_konsultasi', 'konsultasi_oleh_id', 'konsultasi_dimulai_pada']);
        });
    }
}
