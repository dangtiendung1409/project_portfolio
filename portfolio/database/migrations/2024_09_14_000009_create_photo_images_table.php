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
        Schema::create('photo_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('photo_id');
            $table->string('photo_status', 50);
            $table->string('image_url', 255);

            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_images');
        Schema::dropIfExists('photos');
    }
};
