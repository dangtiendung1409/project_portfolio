<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleriesPhoto extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['galleries_id', 'photo_image_id'];
}
