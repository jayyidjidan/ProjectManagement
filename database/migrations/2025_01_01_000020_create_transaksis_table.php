<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->increments('id_transaksi');

            $table->unsignedInteger('id_pembayaran');

            $table->date('tanggal_transaksi');
            $table->decimal('jumlah_transaksi', 15, 2);

            $table->string('bukti_transaksi', 255)->nullable();

            $table->string('metode_pembayaran', 50)->nullable();

            $table->unsignedInteger('id_jenis')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->foreign('id_pembayaran')
                  ->references('id_pembayaran')
                  ->on('pembayarans')
                  ->onDelete('cascade');

            $table->foreign('id_jenis')
                  ->references('id_jenis')
                  ->on('jenis_transaksis')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};