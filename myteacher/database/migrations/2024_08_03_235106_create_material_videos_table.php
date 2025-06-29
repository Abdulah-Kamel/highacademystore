<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('video');
            $table->unsignedBigInteger('chapter_id');
            $table->foreign('chapter_id')->on('chapters')->references('id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_videos');
    }
};
