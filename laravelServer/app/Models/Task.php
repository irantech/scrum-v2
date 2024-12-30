<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    protected $table = 'tasks';

    use SoftDeletes , Timestamp ;
    protected $guarded=[];

    public function user() {
        return $this->belongsTo(User::class) ;
    }

    public function contract() {
        return $this->belongsTo(Contract::class);
    }

    public function taskLabels() {
        return $this->belongsToMany(TaskLabel::class);
    }

    public function todoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable');
    }
    public function lastReferenceTodoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable')->orderBy('created_at', 'desc')->limit(1);
    }

    public function subtasks()
    {
        return $this->morphMany(SubTask::class, 'subtaskable')->whereNull('parent_id');
    }
    public function notAssigendsubtasks()
    {
        return $this->morphMany(SubTask::class, 'subtaskable')->whereNull('parent_id')->whereNull('assigned');
    }

    public function assigendsubtasks()
    {
        return $this->morphMany(SubTask::class, 'subtaskable')->whereNull('parent_id')->whereNotNull('assigned')->groupBy('assigned');
    }
    public function taskTimes()
    {
        return $this->morphMany(taskTime::class, 'tasktimeable');
    }
    public function lastTodoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable')->where('status' , 'in_progress')->orderBy('created_at', 'DESC')->limit(1);
    }
    public function doneTodoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable')->where('status' , 'done');
    }
    public function subTaskError(){
        return $this->morphMany(SubTask::class, 'subtaskable')->whereNull('parent_id')->where('status' , 'error');
    }
    public function subTaskOffer(){
        return $this->morphMany(SubTask::class, 'subtaskable')->whereNull('parent_id')->where('status' , 'offer');
    }
    public function subTaskPeriodic(){
        return $this->morphMany(SubTask::class, 'subtaskable')->whereNull('parent_id')->where('status' , 'periodical');
    }
}
