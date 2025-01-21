<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_id',
        'like_id',
        'comment_id',
        'photo_id',
        'type',
        'content',
        'is_read',
        'notification_date',
    ];

    protected $dates = ['notification_date'];

    /**
     * Mối quan hệ với người dùng nhận thông báo.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Mối quan hệ với người dùng nhận thông báo.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    /**
     * Mối quan hệ với like liên quan.
     */
    public function like()
    {
        return $this->belongsTo(Like::class);
    }

    /**
     * Mối quan hệ với comment liên quan.
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Mối quan hệ với photo image liên quan.
     */
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
    public $timestamps = false;
}
