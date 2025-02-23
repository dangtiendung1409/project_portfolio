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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->onDelete('cascade'); // Người chặn
            $table->foreignId('blocked_id')->constrained('users')->onDelete('cascade'); // Người bị chặn
            $table->timestamp('blocked_at')->useCurrent(); // Thời gian bị chặn
            $table->unique(['blocker_id', 'blocked_id']); // Đảm bảo không có bản ghi trùng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
