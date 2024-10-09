<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffRequest extends Model
{
    use SoftDeletes , Timestamp;


    protected $fillable =[
        'user_id' ,
        'manager_id' ,
        'has_confirmed' ,
        'reason' ,
        'manager_reason' ,
        'requestable_type' ,
        'requestable_id'
    ];

    public function User() {
        return $this->belongsTo(User::class  , 'user_id')->with(['role']);
    }
    public function userRole($section) {
        return $this->belongsTo(User::class  , 'user_id')->with(['role' => function($q) use ($section)  {
            $q->where('section_id' , $section);
        }]);
    }

    public function manager() {
        return $this->belongsTo(User::class , 'manager_id');
    }

    public function requestable()
    {
        return $this->morphTo();
    }

    public function todoList()
    {
        return $this->morphedByMany(ToDoList::class, 'requestable');
    }

}
