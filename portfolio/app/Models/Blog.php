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
        'image', // Sửa từ 'images' thành 'image' để khớp với controller
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title) . '-' . time();
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
