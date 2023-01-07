<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facebook extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'facebook';
 
    protected $fillable = [
        'user_id', 'facebook_id', 'access_token',
    ];
}
