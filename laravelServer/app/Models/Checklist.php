<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Checklist extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $fillable = ['title'  , 'language'];

    protected static $logAttributes = ['title' , 'language_id'];
    protected static $logName = 'checklist_activities';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This checklist has been {$eventName}";
    }

    public function titleChecklists() {
        return $this->hasMany(TitleChecklist::class);
    }
    public function contracts() {
        return $this->belongsToMany(Contract::class , 'checklist_contract' , 'checklist_id' , 'contract_id');
    }
    public function language() {
        return $this->belongsTo(Language::class);
    }

    public function TaskTimes()
    {
        return $this->morphMany(taskTime::class, 'tasktimeable');
    }
}
