<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstagramPost extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'instagram_posts';
 
    protected $fillable = [
        'link', 'instagram_id', 'media_id','media_type','uploaded_at','response'
    ];
}
