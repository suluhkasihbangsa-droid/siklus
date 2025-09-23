<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pemeriksaans', function (Blueprint $table) {
            // LANGKAH 1: Hapus dulu foreign key yang sudah ada.
            // Laravel secara default memberi nama constraint: nama_tabel_nama_kolom_foreign
            $table->dropForeign(['sasaran_id']);

            // LANGKAH 2: Setelah dihapus, tambahkan lagi foreign key yang baru
            // dengan aturan onDelete('cascade') yang kita inginkan.
            $table->foreign('sasaran_id')
                  ->references('id')
                  ->on('sasarans')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemeriksaans', function (Blueprint $table) {
            // Untuk proses rollback, kita hapus key yang baru...
            $table->dropForeign(['sasaran_id']);

            // ...dan kembalikan ke versi semula (tanpa cascade) agar konsisten.
            $table->foreign('sasaran_id')
                  ->references('id')
                  ->on('sasarans');
        });
    }
};