<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'photo_id','gallery_id', 'like_date'];

    protected $dates = ['like_date'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }
    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'like_id');
    }

}
