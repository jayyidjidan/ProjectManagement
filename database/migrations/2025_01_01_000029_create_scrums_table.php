<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrums', function (Blueprint $table) {
            $table->increments('id_scrum');
            $table->string('day', 10); // e.g. Senin, Selasa
            $table->date('date_scrum');
            $table->unsignedInteger('id_responsible')->nullable();
            $table->string('scrum_password', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_responsible')
                  ->references('id_member')->on('members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrums');
    }
};
