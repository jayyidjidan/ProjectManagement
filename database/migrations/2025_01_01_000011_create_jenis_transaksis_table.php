<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_transaksis', function (Blueprint $table) {
            $table->increments('id_jenis');
            $table->string('nama_jenis', 100); // e.g. DP, Pelunasan, Termin 1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_transaksis');
    }
};
