<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtasks', function (Blueprint $table) {
            $table->increments('id_subtask');

            // Parent Task
            $table->unsignedInteger('id_task');

            $table->string('subtask_name', 200);

            $table->date('subtask_deadline')->nullable();

            $table->unsignedInteger('id_priority')->nullable();

            $table->unsignedInteger('id_status')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->foreign('id_task')
                  ->references('id_task')
                  ->on('tasks')
                  ->onDelete('cascade');

            $table->foreign('id_priority')
                  ->references('id_priority')
                  ->on('priorities')
                  ->onDelete('set null');

            $table->foreign('id_status')
                  ->references('id_status')
                  ->on('status_tasks')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};