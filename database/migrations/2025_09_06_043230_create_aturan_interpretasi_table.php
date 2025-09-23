<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAturanInterpretasiTable extends Migration
{
    public function up()
    {
        Schema::create('aturan_interpretasi', function (Blueprint $table) {
            $table->id();
            $table->string('kategori')->index()->comment('Cth: IMT, LP, LILA_DEWASA, LILA_BALITA, TENSI, GULA_DARAH, ASAM_URAT, KOLESTEROL');
            $table->string('nama_interpretasi');
            $table->string('kode_interpretasi')->nullable();
            
            // Kolom untuk kondisi
            $table->char('kondisi_gender', 1)->nullable()->comment('L atau P. NULL untuk semua gender.');
            $table->integer('kondisi_usia_min_bulan')->nullable()->comment('Usia minimal dalam bulan. NULL untuk tanpa batas bawah.');
            $table->integer('kondisi_usia_max_bulan')->nullable()->comment('Usia maksimal dalam bulan. NULL untuk tanpa batas atas.');
            $table->string('kondisi_metode')->nullable()->comment('Cth: Puasa, Sewaktu. Untuk Gula Darah.');

            // Kolom untuk nilai/batas
            $table->decimal('batas_bawah', 8, 2)->nullable();
            $table->decimal('batas_atas', 8, 2)->nullable();
            
            // Kolom untuk Tensi (kasus khusus)
            $table->integer('batas_sistolik')->nullable();
            $table->integer('batas_diastolik')->nullable();
            
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aturan_interpretasi');
    }
}