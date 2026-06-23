<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrum_members', function (Blueprint $table) {
            $table->unsignedInteger('id_scrum');
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_status')->nullable();
            $table->primary(['id_scrum', 'id_member']);
            $table->timestamps();

            $table->foreign('id_scrum')
                  ->references('id_scrum')->on('scrums')
                  ->onDelete('cascade');

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');

            $table->foreign('id_status')
                  ->references('id_status')->on('status_members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrum_members');
    }
};
