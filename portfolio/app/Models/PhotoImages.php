<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoImages extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'image_url',
        'photo_status',
        'photo_id'


    ];
    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function reports()
    {
        return $this->hasMany(Report::class, 'photo_image_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function galleries()
    {
        return $this->belongsToMany(Gallery::class, 'galleries_photos', 'photo_image_id', 'galleries_id');
    }
}
