<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id_member');
            $table->unsignedInteger('id_user')->unique(); // 1-to-1 with users
            $table->string('member_name', 150);
            $table->unsignedInteger('id_position');
            $table->date('joined_date')->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->integer('total_cuti')->default(0);
            $table->integer('total_WFH')->default(0);
            $table->integer('total_overtime')->default(0);
            $table->integer('point')->default(0);
            $table->unsignedInteger('id_status')->nullable();
            $table->string('work_location', 20)->default('onsite'); // onsite / offsite
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')->on('users')
                  ->onDelete('cascade');

            $table->foreign('id_position')
                  ->references('id_position')->on('jabatans')
                  ->onDelete('restrict');

            $table->foreign('id_status')
                  ->references('id_status')->on('status_members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
