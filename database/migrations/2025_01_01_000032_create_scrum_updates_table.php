<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrum_updates', function (Blueprint $table) {
            $table->increments('id_scrum_update');
            $table->unsignedInteger('id_scrum');
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_task')->nullable();
            $table->string('task_target', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_scrum')
                  ->references('id_scrum')->on('scrums')
                  ->onDelete('cascade');

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');

            $table->foreign('id_task')
                  ->references('id_task')->on('tasks')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrum_updates');
    }
};
