<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_tasks', function (Blueprint $table) {
            $table->increments('id_status');
            $table->string('status_name', 50); // e.g. open, in_progress, done, blocked
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_tasks');
    }
};
