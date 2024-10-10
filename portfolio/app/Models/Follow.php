<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['follower_id', 'following_id', 'follow_date'];

    protected $dates = ['follow_date'];

    //Người theo dõi
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
    // Đang theo dõi
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
