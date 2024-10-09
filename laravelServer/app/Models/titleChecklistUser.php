<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class titleChecklistUser extends Model
{

    protected $table = 'title_checklist_user';
    protected $fillable = [
        'checklist_contract_id' ,'status' , 'section_id' , 'user_id' , 'titleChecklist_id',
    ];
    public function contractChecklists() {
        return $this->hasMany(ChecklistContract::class);
    }

    public function titleChecklists(){
        return $this->belongsTo(TitleChecklist::class);
    }


}
