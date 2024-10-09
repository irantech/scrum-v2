<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\Traits\LogsActivity;

class role extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected static $logAttributes = ['title'];
    protected $fillable = ['title'];
    protected static $logName = 'role_activities';
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This model has been {$eventName}";
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    public function section() {
        return $this->belongsTo(Section::class);
    }

}
