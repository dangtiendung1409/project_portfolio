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
            $table->foreignId('photo_image_id')->constrained('photo_images')->onDelete('cascade');
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade'); // người báo cáo
            $table->foreignId('violator_id')->constrained('users')->onDelete('cascade'); // người đăng ảnh (người bị tố cáo)
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
