<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignee_subtasks', function (Blueprint $table) {
            $table->unsignedInteger('id_subtask');
            $table->unsignedInteger('id_member');
            $table->primary(['id_subtask', 'id_member']);
            $table->timestamps();

            $table->foreign('id_subtask')
                  ->references('id_subtask')->on('subtasks')
                  ->onDelete('cascade');

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignee_subtasks');
    }
};
