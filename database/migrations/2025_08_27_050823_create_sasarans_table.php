<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSasaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
    */
    public function up()
    {
        Schema::create('sasarans', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->date('tgl_lahir');
            $table->string('gender', 1); // Cukup 1 karakter: 'L' atau 'P'
            $table->string('no_hp', 15)->nullable();

            // Kolom untuk alamat terstruktur
            $table->unsignedBigInteger('provinsi_id');
            $table->unsignedBigInteger('kota_id');
            $table->unsignedBigInteger('kecamatan_id');
            $table->unsignedBigInteger('kelurahan_id');
            $table->text('alamat_detail')->nullable(); // Untuk RT/RW/Nama Jalan

            $table->timestamps();

            // Catatan: Relasi (foreign key) akan kita tambahkan setelah membuat tabel alamat
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sasarans');
    }
}
