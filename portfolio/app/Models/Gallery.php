<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Gallery extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['galleries_name', 'galleries_description', 'user_id','visibility','galleries_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class, 'gallery_id');
    }
    public function photo()
    {
        return $this->belongsToMany(Photo::class, 'galleries_photos', 'galleries_id', 'photo_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($gallery) {
            // Xóa ảnh trong bảng galleries_photos
            $gallery->photo()->detach();
        });
    }
}
