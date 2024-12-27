<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name','slug','image'];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    public $timestamps = false;
}
