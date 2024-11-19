<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubTask extends Model
{

    protected $table = 'subtasks';
    protected $fillable = ['description'  ,'status' , 'user_id' , 'order','key_id'];

    public function replies() {
        return $this->hasMany(SubTask::class,'parent_id');
    }

    public function lastReplies() {
        return $this->hasMany(SubTask::class,'key_id')->latest();
    }

    public function allReplies()
    {
        return $this->replies()->with('allReplies');
    }

    public function countAcceptReplies()
    {
        return $this->allReplies()->where('status', 'accept')->latest()->first();
    }

    public function countRejectReplies()
    {


        return $this->allReplies()->where('status', 'reject')->latest()->first();
    }



    public function allAcceptReplies() {
        return $this->allReplies();
    }
    public function allRejectReplies() {
        return $this->allReplies();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function assignedUser(){

        return $this->belongsTo(User::class , 'assigned' , 'id');
    }

    public function assignedSection(){
        return $this->belongsTo(User::class , 'assigned' , 'id')->with('roleSection');
    }
    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function subtaskable()
    {
        return $this->morphTo();
    }
}
