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
        Schema::create('movies_genres', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('movie_id');
            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')->onDelete('cascade');
            $table->bigInteger('tag_id');
            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies_genres');
    }
};
