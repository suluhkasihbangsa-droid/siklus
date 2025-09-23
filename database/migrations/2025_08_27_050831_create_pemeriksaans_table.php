<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemeriksaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemeriksaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_id')->constrained()->onDelete('cascade');
            // $table->foreignId('event_id')->nullable(); // Kita siapkan untuk nanti
            
            $table->string('usia_saat_pemeriksaan'); // Cth: "27 tahun 3 bulan"
            $table->float('bb')->comment('Berat Badan (kg)');
            $table->float('tb')->comment('Tinggi Badan (cm)');
            $table->float('imt')->comment('Indeks Massa Tubuh');
            $table->float('lila')->nullable()->comment('Lingkar Lengan Atas (cm)');
            $table->float('lp')->nullable()->comment('Lingkar Perut (cm)');
            
            $table->integer('tensi_sistolik')->nullable();
            $table->integer('tensi_diastolik')->nullable();

            $table->integer('gd')->nullable()->comment('Gula Darah (mg/dL)');
            $table->string('mgd', 1)->nullable()->comment('Metode Gula Darah: S=Sewaktu, P=Puasa');
            $table->float('asut')->nullable()->comment('Asam Urat (mg/dL)');
            $table->integer('koles')->nullable()->comment('Kolesterol (mg/dL)');

            // Kolom untuk menyimpan hasil interpretasi
            $table->string('int_imt');
            $table->string('int_lila');
            $table->string('int_lp');
            $table->string('int_tensi');
            $table->string('int_gd');
            $table->string('int_asut');
            $table->string('int_koles');

            $table->text('keluhan_awal')->nullable();
            $table->date('tanggal_pemeriksaan'); // Tambahan penting
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
        Schema::dropIfExists('pemeriksaans');
    }
}
