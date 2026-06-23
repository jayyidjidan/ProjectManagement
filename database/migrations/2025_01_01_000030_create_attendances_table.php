<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->increments('id_attendance');
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_scrum')->nullable();
            $table->unsignedInteger('id_status')->nullable();
            $table->time('start_hour')->nullable();
            $table->time('leave_hour')->nullable();
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');

            $table->foreign('id_scrum')
                  ->references('id_scrum')->on('scrums')
                  ->onDelete('set null');

            $table->foreign('id_status')
                  ->references('id_status')->on('status_members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
