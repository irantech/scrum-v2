<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingSession extends Model
{
    use SoftDeletes , Timestamp;

    protected $table = 'training_sessions';

    protected $fillable = [
        'user_id' ,
        'checklist_contract_id' ,
        'session_date' ,
        'session_time' ,
        'location_status',
        'status',
    ];


    public function contractChecklist() {
        return $this->belongsTo(ChecklistContract::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function todoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable');
    }
}
