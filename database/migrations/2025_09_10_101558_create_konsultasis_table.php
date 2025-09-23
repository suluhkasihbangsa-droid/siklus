<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKonsultasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konsultasis', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel pemeriksaan
            $table->foreignId('pemeriksaan_id')->constrained('pemeriksaans')->onDelete('cascade');

            // Relasi ke tabel user (dokter)
            // Dibuat nullable dan nullOnDelete agar jika dokter dihapus, riwayat konsultasi tidak ikut terhapus
            $table->foreignId('dokter_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('keluhan')->nullable();
            $table->text('diagnosa');
            $table->text('rekomendasi');
            $table->text('resep_obat')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('konsultasis');
    }
}
