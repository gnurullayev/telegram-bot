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
        Schema::create('movies_tags', function (Blueprint $table) {
            $table->id();
            // O'zgartirish: movie_id va genre_id ni bigint turiga o'zgartiramiz
            $table->bigInteger('movie_id');
            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')->onDelete('cascade');
            $table->bigInteger('genre_id');
            $table->foreign('genre_id')
                ->references('id')
                ->on('genres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies_tags');
    }
};
