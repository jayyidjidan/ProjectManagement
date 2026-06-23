<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_activities', function (Blueprint $table) {
            $table->increments('id_activity');
            $table->unsignedInteger('id_task');
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_type');
            $table->text('message')->nullable();
            $table->string('old_value', 255)->nullable();
            $table->string('new_value', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            // No updated_at — activity logs are immutable

            $table->foreign('id_task')
                  ->references('id_task')->on('tasks')
                  ->onDelete('cascade');

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');

            $table->foreign('id_type')
                  ->references('id_type')->on('activity_types')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_activities');
    }
};
