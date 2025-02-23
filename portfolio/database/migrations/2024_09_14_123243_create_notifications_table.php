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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('like_id')->nullable()->constrained('likes');
            $table->foreignId('comment_id')->nullable()->constrained('comments');
            $table->foreignId('photo_id')->nullable()->constrained('photos');
            // type 0 = like , type 1 = comment , type 2 = follow
            $table->unsignedTinyInteger('type');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->dateTime('notification_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
