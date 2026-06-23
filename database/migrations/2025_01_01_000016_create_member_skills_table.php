<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_skills', function (Blueprint $table) {
            $table->unsignedInteger('id_member');
            $table->unsignedInteger('id_skill');
            $table->primary(['id_member', 'id_skill']);
            $table->timestamps();

            $table->foreign('id_member')
                  ->references('id_member')->on('members')
                  ->onDelete('cascade');

            $table->foreign('id_skill')
                  ->references('id_skill')->on('skills')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_skills');
    }
};
