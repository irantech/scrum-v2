<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $guarded=[];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function checklistContract() {
        return $this->belongsTo(ChecklistContract::class);
    }
}
