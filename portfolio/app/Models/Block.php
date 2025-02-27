<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = ['blocker_id', 'blocked_id'];
    public $timestamps = false;
    public $incrementing = false; // Không sử dụng ID tự tăng
    protected $primaryKey = null; // Không có khóa chính mặc định

    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blocked()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }
}

