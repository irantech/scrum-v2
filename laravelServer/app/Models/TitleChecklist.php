<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class TitleChecklist extends Model
{
    use SoftDeletes,LogsActivity;
    protected $fillable = ['title'];
    //log boot
    protected static $logAttributes = ['title'];
    protected static $logName = 'title_checklist_activities';
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This titleChecklist has been {$eventName}";
    }

//    public function tapActivity(Activity $activity, string $eventName)
//    {
//        dd($activity->subject->sections);
//       // $activity->sections = $this->sections;
//    }

    public function sections(){
        return $this->belongsToMany(Section::class , 'section_title_checklist' , 'titleChecklist_id' ,'section_id')
            ->withTimestamps();
    }
    public function checklist() {
        return $this->belongsTo(Checklist::class);
    }
    public function users() {
        return $this->belongsToMany(User::class , 'title_checklist_user' , 'titleChecklist_id' , 'user_id')->withTrashed()->withTimestamps();
    }
}
