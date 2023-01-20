<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacebookPost extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'facebook_posts';
 
    protected $fillable = [
        'link', 'facebook_id', 'shares','response','likes'
    ];
}
