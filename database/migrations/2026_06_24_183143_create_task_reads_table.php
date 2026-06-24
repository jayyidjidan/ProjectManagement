<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_task'); // Sesuai int unsigned di tabel tasks
            $table->unsignedInteger('id_member'); // Sesuai int unsigned di tabel members
            $table->timestamp('last_read_at')->useCurrent();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_task')->references('id_task')->on('tasks')->onDelete('cascade');
            $table->foreign('id_member')->references('id_member')->on('members')->onDelete('cascade');
            
            // Indeks unik agar satu member hanya punya satu catatan per task
            $table->unique(['id_task', 'id_member']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_reads');
    }
};