<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $fillable = ['galleries_name', 'galleries_description', 'user_id', 'creation_date'];

    protected $dates = ['creation_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'galleries_photos');
    }
}
