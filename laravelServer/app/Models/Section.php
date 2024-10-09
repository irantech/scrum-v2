<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function titleChecklists() {
        return $this->belongsToMany(TitleChecklist::class , 'section_title_checklist' , 'section_id' , 'titleChecklist_id')->withTimestamps();
    }

    public function roles(){
        return $this->hasMany(role::class);
    }

    public function taskTimes()
    {
        return $this->belongsToMany(taskTime::class);
    }

}
