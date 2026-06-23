<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyeks', function (Blueprint $table) {
            $table->increments('id_proyek');
            $table->string('nama_proyek', 200);
            $table->date('deadline')->nullable();
            $table->unsignedInteger('id_status');
            $table->unsignedInteger('id_klien');
            $table->unsignedInteger('id_pembayaran')->nullable();
            $table->unsignedInteger('id_tipe')->nullable();
            $table->unsignedInteger('id_project_manager')->nullable();
            $table->timestamps();

            $table->foreign('id_status')
                  ->references('id_status')->on('status_proyeks')
                  ->onDelete('restrict');

            $table->foreign('id_klien')
                  ->references('id_klien')->on('kliens')
                  ->onDelete('restrict');

            $table->foreign('id_pembayaran')
                  ->references('id_pembayaran')->on('pembayarans')
                  ->onDelete('set null');

            $table->foreign('id_tipe')
                  ->references('id_tipe')->on('tipes')
                  ->onDelete('set null');

            $table->foreign('id_project_manager')
                  ->references('id_member')->on('members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyeks');
    }
};
