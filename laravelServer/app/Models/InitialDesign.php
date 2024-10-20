<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InitialDesign extends Model
{
    protected  $guarded=[];

    public function checklistContract()
    {
       return $this->belongsTo(ChecklistContract::class);
    }

}
