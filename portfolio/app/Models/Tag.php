<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = ['tag_name'];
    public $timestamps = false;
    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'photo_tags', 'tag_id', 'photo_id');
    }
}
