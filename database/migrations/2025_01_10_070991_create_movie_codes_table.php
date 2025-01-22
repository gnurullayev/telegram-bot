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
        Schema::create('movie_codes', function (Blueprint $table) {
            $table->id();
            $table->string("link")->nullable();
            $table->foreignId('movie_id')
                ->nullable()
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie_codes', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropForeign(['series_id']);
        });
        Schema::dropIfExists('movie_codes');
    }
};
