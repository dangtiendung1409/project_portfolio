<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'content',
        'cover_image',
        'images',
    ];

    protected $casts = [
        'images' => 'array', // Ép kiểu JSON thành array
    ];

    /**
     * Tự động tạo slug từ title nếu chưa có
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title) . '-' . time();
            }
        });
    }

    /**
     * Quan hệ với User (tác giả)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
