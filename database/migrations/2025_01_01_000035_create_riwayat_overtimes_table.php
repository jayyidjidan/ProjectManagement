<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_overtimes', function (Blueprint $table) {
            $table->increments('id_riwayat');
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_attendance');
            $table->date('tanggal');
            $table->decimal('durasi_jam', 5, 2); // e.g. 2.50 hours
            $table->string('status_approval', 20)->default('pending'); // pending/approved/rejected
            $table->unsignedInteger('id_approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');

            $table->foreign('id_attendance')
                  ->references('id_attendance')->on('attendances')
                  ->onDelete('cascade');

            $table->foreign('id_approved_by')
                  ->references('id_member')->on('members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_overtimes');
    }
};
