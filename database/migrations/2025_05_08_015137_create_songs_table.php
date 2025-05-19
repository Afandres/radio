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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('duration')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->string('file'); // file path
            $table->time('scheduled_time')->nullable(); // optional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
