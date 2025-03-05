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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade'); // người báo cáo
            $table->foreignId('violator_id')->constrained('users')->onDelete('cascade'); // người đăng ảnh (người bị tố cáo)
            $table->foreignId('photo_id')->nullable()->constrained('photos')->onDelete('cascade');
            $table->foreignId('comment_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->foreignId('gallery_id')->nullable()->constrained('galleries')->onDelete('cascade');
            $table->integer('type')->default(0)->comment('0: Photo, 1: Comment, 2: Gallery');
            $table->text('report_reason');
            $table->dateTime('report_date');
            $table->string('status', 50);
            $table->string('action_taken', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
