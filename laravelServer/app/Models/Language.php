<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Language extends Model
{
    use LogsActivity ;
    protected $fillable = ['title'];
    protected static $logAttributes = ['title'];
    protected static $logName = 'language_activities';
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This language has been {$eventName}";
    }

    public function checklists() {
        return $this->belongsToMany(Checklist::class);
    }
}
