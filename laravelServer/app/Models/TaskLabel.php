<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskLabel extends Model
{
    protected $fillable = ['title' , 'color'] ;
    use SoftDeletes , Timestamp;

    public function tasks() {
        return $this->belongsToMany(Task::class);
    }
}
