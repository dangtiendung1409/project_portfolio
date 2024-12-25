<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'photo_image_id', 'like_date'];

    protected $dates = ['like_date'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photoImage()
    {
        return $this->belongsTo(PhotoImages::class, 'photo_image_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'like_id');
    }

}
