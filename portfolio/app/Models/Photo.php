<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'upload_date',
        'location',
        'user_id',
        'category_id',
        'privacy_status',
    ];
    public function images()
    {
        return $this->hasMany(PhotoImages::class, 'photo_id');
    }
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

    protected $dates = ['upload_date'];
    public $timestamps = false;
}
