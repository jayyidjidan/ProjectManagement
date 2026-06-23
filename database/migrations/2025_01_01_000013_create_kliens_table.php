<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kliens', function (Blueprint $table) {
            $table->increments('id_klien');
            $table->string('nama_klien', 150);
            $table->string('no_telp', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->unsignedInteger('id_sumber_klien')->nullable();
            $table->string('asal_negara', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_sumber_klien')
                  ->references('id_sumberklien')->on('sumber_kliens')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kliens');
    }
};
