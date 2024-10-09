<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistProcess extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function section() {
        return $this->belongsTo(Section::class);
    }
    public function checklist() {
        return $this->belongsTo(Checklist::class);
    }
    public function contract() {
        return $this->belongsTo(Contract::class);
    }

    public function contractChecklists(){
        return $this->hasMany(ChecklistContract::class);
    }

//    public function subtasks(){
//        return $this->hasMany(SubTask::class)->withCount('replies')->whereNull('parent_id')
//            ->orderBy('replies_count' , 'ASC')
//            ->orderByRaw('FIELD(seen,0) DESC')
//            ->orderBy('order' , 'DESC');
//    }

    public function subtasks()
    {

        return $this->morphMany(SubTask::class, 'subtaskable')->withCount('replies')->whereNull('parent_id')
            ->orderBy('replies_count' , 'ASC')
            ->orderByRaw('FIELD(seen,0) DESC')
            ->orderBy('order' , 'DESC');
    }

    public function subTaskError(){
        return $this->hasMany(SubTask::class)->with('lastReplies')->whereNull('parent_id')->where('status' , 'error');

    }
    public function subTaskOffer(){
        return $this->hasMany(SubTask::class)->with('lastReplies')->whereNull('parent_id')->where('status' , 'offer');
    }

    public function subTaskPeriodic(){
        return $this->hasMany(SubTask::class)->with('lastReplies')->whereNull('parent_id')->where('status' , 'periodical');

    }

    public function allAcceptReplies() {
        return $this->hasMany(SubTask::class,'parent_id')->where('status' , 'accept')->with('allAcceptReplies')->latest();
    }
    public function allRejectReplies() {
        return $this->hasMany(SubTask::class,'parent_id')->where('status' , 'reject')->with('allRejectReplies')->latest();
    }


    public function checklistReversesAcceptOffers(){
        return $this->hasMany(SubTask::class)->withCount('allAcceptReplies')->whereNull('parent_id')->where('status' , 'offer');
    }

    public function checklistReversesRejectOffers(){
        return $this->hasMany(SubTask::class)->withCount('allRejectReplies')->whereNull('parent_id')->where('status' , 'offer');
    }

    public function checklistReversesAcceptPeriodic(){
        return $this->hasMany(SubTask::class)->withCount('allAcceptReplies')->whereNull('parent_id')->where('status' , 'periodic');
    }

    public function checklistReversesRejectPeriodic(){
        return $this->hasMany(SubTask::class)->with(['checklistReversesPeriodic' => function($query_reverse) {
            $query_reverse->withCount('allRejectReplies');
        }])->whereNull('parent_id')->where('status' , 'periodic');
    }

    public function titleChecklistUser() {
        return $this->join('title_checklist_user', 'checklist_processes.section_id', '=', 'title_checklist_user.section_id')->where('checklist_processes.status' , 0);
    }
}
