<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['photo_image_id', 'user_id', 'comment_text', 'comment_date','comment_status'];

    protected $dates = ['comment_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photoImage()
    {
        return $this->belongsTo(PhotoImages::class, 'photo_image_id');
    }
}
