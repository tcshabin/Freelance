<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YoutubeReports extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'youtube_reports';
 
    protected $fillable = [
        'channel_id', 'date', 'views','likes','sub_get','response'
    ];
}
