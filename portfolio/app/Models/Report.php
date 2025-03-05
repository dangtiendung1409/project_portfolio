<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'violator_id',
        'photo_id',
        'comment_id',
        'gallery_id',
        'type',
        'report_reason',
        'report_date',
        'status',
        'action_taken'
    ];

    protected $dates = ['report_date'];

    public $timestamps = false;

    /**
     * Quan hệ với bảng Photo
     */
    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }

    /**
     * Quan hệ với bảng Comment
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    /**
     * Quan hệ với bảng Gallery
     */
    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id');
    }

    /**
     * Quan hệ với người báo cáo (reporter)
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Quan hệ với người bị tố cáo (violator)
     */
    public function violator()
    {
        return $this->belongsTo(User::class, 'violator_id');
    }
}
