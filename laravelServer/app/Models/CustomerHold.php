<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerHold extends Model
{
    use  Timestamp;
    protected $fillable = ['user_id' , 'starting_time' , 'ending_time' , 'difference_time' , 'checklist_contract_id'] ;
    public function User() {
        return $this->belongsTo(User::class);
    }
    public function checklistContract()
    {
        return $this->belongsTo(ChecklistContract::class);
    }

    public function Section(){
        return $this->belongsTo(Section::class);
    }
}
