<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('description');
        $table->datetime('start_date');
        $table->datetime('end_date');
        $table->string('status');
        $table->datetime('confirmed_date')->nullable();
        $table->text('comment')->nullable();
        $table->unsignedBigInteger('user_id'); 
        $table->unsignedBigInteger('phase_id'); 
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('phase_id')->references('id')->on('phases');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

