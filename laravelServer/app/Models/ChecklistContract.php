<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChecklistContract extends Model
{
    protected $table = 'checklist_contract' ;
    protected $fillable = ['contract_id' , 'checklist_id'];

    public function titleChecklistUser()
    {
        return $this->hasMany(titleChecklistUser::class , 'checklist_contract_id');
    }

    public function titleChecklistUserAverage()
    {
        return $this->hasMany(titleChecklistUser::class , 'checklist_contract_id')
            ->select('*' , DB::raw('AVG(status) as average'))
            ->groupBy('section_id','checklist_contract_id');
    }

    public function titleChecklistUserComplete()
    {
        return $this->hasMany(titleChecklistUser::class , 'checklist_contract_id')
            ->select('*' , DB::raw('AVG(status) as average'))
            ->groupBy('checklist_contract_id');
    }

    public function checklistProcess() {
        return $this->hasMany(ChecklistProcess::class , 'checklist_contract_id');
    }

    public function subTaskProcess() {
        return $this->hasMany(ChecklistProcess::class , 'checklist_contract_id')->with(['subTaskError' , 'subTaskOffer' , 'subTaskPeriodic'])->where('checklist_processes.status' , 0);
    }
    public function reversedChecklistProcessDate($params) {
            $query = $this->hasMany(ChecklistProcess::class , 'checklist_contract_id');
             if(isset($request['start_date']) && !empty($request['start_date'])  ){
                 $start_time = Verta::parse($request['start_date'])->datetime()->format('y-m-d');
                 $query .= $query->whereDate('created_at', '>=' , $start_time);
             }
            if(isset($request['end_date']) && !empty($request['end_date'])) {
                $end_time = Verta::parse($request['end_date'])->datetime()->format('y-m-d');
                $query .= $query->whereDate('created_at', '<=' , $end_time);
            }
            $query .= $query->where('status' , 0);
            return $query ;
    }
    public function checklist () {
        return $this->belongsTo(Checklist::class);
    }
    public function contract() {
        return $this->belongsTo(Contract::class);
    }

    public function todoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable');
    }
    public function doneTodoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable')->where('status' , 'done');
    }
    public function lastTodoList()
    {
        return $this->morphMany(ToDoList::class, 'todoable')->where('status' , 'in_progress')->orderBy('created_at', 'DESC')->limit(1);
    }
    public function trainingSessions(){
        return $this->hasMany(TrainingSession::class);
    }
    public function customerHold(){
        return $this->hasMany(CustomerHold::class);
    }

    public function initialDesign()
    {
        return $this->hasOne(initialDesign::class);
    }
}
