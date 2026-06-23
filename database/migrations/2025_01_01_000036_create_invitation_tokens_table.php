<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 150);
            $table->unsignedInteger('id_role');
            $table->string('token', 64)->unique();
            $table->boolean('is_used')->default(false);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->foreign('id_role')
                  ->references('id_role')->on('roles')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_tokens');
    }
};
