<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id_task');
            $table->unsignedInteger('id_proyek')->nullable(); // nullable = personal task
            $table->string('nama_task', 200);
            $table->date('deadline_task')->nullable();
            $table->unsignedInteger('id_priority')->nullable();
            $table->unsignedInteger('id_status')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('id_proyek')
                  ->references('id_proyek')->on('proyeks')
                  ->onDelete('cascade');

            $table->foreign('id_priority')
                  ->references('id_priority')->on('priorities')
                  ->onDelete('set null');

            $table->foreign('id_status')
                  ->references('id_status')->on('status_tasks')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
