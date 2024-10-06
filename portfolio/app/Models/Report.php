<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'photo_image_id',
        'reporter_id',
        'violator_id',
        'report_reason',
        'report_date',
        'status',
        'action_taken'
    ];

    protected $dates = ['report_date'];

    public function photoImage()
    {
        return $this->belongsTo(PhotoImages::class, 'photo_image_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function violator()
    {
        return $this->belongsTo(User::class, 'violator_id');
    }
}
