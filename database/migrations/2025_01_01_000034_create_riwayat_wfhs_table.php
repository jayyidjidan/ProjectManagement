<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_wfhs', function (Blueprint $table) {
            $table->increments('id_riwayat');
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_scrum')->nullable();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');

            $table->foreign('id_scrum')
                  ->references('id_scrum')->on('scrums')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_wfhs');
    }
};
