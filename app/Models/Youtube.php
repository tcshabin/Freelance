<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Youtube extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'youtube';
 
    protected $fillable = [
        'user_id', 'youtube_id', 'access_token',
    ];
}
