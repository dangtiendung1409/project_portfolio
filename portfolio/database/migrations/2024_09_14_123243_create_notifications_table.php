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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('sender');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade')->comment('recipient');
            $table->foreignId('like_id')->nullable()->constrained('likes');
            $table->foreignId('comment_id')->nullable()->constrained('comments');
            $table->foreignId('gallery_id')->nullable()->constrained('galleries');
            $table->foreignId('photo_id')->nullable()->constrained('photos');
            // type 0 = like photo, type 1 = comment , type 2 = follow , type 3 = like gallery
            $table->unsignedTinyInteger('type')->comment('0 = like photo, 1 = comment, 2 = follow, 3 = like gallery');
            $table->text('content');
            $table->boolean('is_read')->default(false)->comment('0 = unread, 1 = read');
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
