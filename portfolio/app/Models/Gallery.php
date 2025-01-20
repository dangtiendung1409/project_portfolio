<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $fillable = ['galleries_name', 'galleries_description', 'user_id','visibility','galleries_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photoImages()
    {
        return $this->belongsToMany(PhotoImages::class, 'galleries_photos', 'galleries_id', 'photo_image_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($gallery) {
            // Xóa ảnh trong bảng galleries_photos
            $gallery->photoImages()->detach();
        });
    }
}
