<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'upload_date',
        'location',
        'user_id',
        'category_id',
        'photo_status',
        'privacy_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'photo_tags', 'photo_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function galleries()
    {
        return $this->belongsToMany(Gallery::class, 'galleries_photos');
    }
    protected $dates = ['upload_date'];
    public $timestamps = false;
}
