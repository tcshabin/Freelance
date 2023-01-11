<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Google extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'google';
 
    protected $fillable = [
        'user_id', 'google_id', 'access_token',
    ];
}
