<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instagram extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'instagram';
 
    protected $fillable = [
        'user_id', 'insta_id', 'access_token',
    ];
}
