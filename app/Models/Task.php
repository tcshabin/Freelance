<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    
    protected $table = 'task';

    protected $fillable = ['name','priority','project'];

    public function project()
    {
        return $this->hasOne('App\Models\Projects', 'id', 'project')->select(['id', 'name']);
    }

    public function scopeSearch($query,$keyword)
    {
        if ($keyword != '') {
            return $query->orWhere('projects.name', 'LIKE', '%' . $keyword . '%')->orWhere('task.name', 'LIKE', '%' . $keyword . '%');
    
        }
    }
    public function scopeFilter($query,$project)
    {
        if ($project != '') {
            return $query->where('task.project',$project);
        }
    }        
}
