<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Videos extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'videos';
    
    protected $fillable = [
        'video_id', 'view_count','like_count','watch_time','type','video_response','channel_id'
    ];
}
