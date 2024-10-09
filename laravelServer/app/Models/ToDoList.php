<?php

namespace App\Models;

use _HumbugBox7eb78fbcc73e\Amp\Parallel\Sync\ChannelException;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ToDoList extends Model
{
    use SoftDeletes , Timestamp;
    protected $table = 'todo_lists';

    protected $fillable =[
      'user_id' ,
      'title' ,
      'status' ,
      'todoable_type' ,
      'todoable_id' ,
      'starting_time' ,
      'ending_time' ,
      'change_time_reason' ,
      'difference_time' ,
      'todo_status'
    ];

    public function User() {
       return $this->belongsTo(User::class);
    }

    public function todoable()
    {
        return $this->morphTo();
    }

    public function checklistContract()
    {
        return $this->morphedByMany(ChecklistContract::class, 'todoable');
    }
    public function tasks()
    {

        return $this->morphedByMany(Task::class, 'todoable');
    }

    public function trainingSessions()
    {
        return $this->morphedByMany(TrainingSession::class, 'todoable');
    }

    public function requests()
    {
        return $this->morphMany(StaffRequest::class, 'requestable');
    }
    public function getAllTask()
    {
        return $this->with('todoable')->where('todoable_type' , 'App\Models\Task')->get();
    }
}
