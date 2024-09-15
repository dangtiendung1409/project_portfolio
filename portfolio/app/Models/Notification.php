<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'notification_type', 'content', 'is_read', 'notification_date'];

    protected $dates = ['notification_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
