<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChecklistReverse extends Model
{
    protected $table = 'checklist_reverse';

    public function replies() {
        return $this->hasMany(ChecklistReverse::class,'parent_id');
    }

    public function lastReplies() {
        return $this->hasOne(ChecklistReverse::class,'key_id')->latest();
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
    public function section(){
        return $this->belongsTo(Section::class);
    }
}
