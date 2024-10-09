<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class UserMeta extends Model
{
    use SoftDeletes , LogsActivity;
    protected $table = 'user_meta';

    protected static $logName = 'user_meta';
    protected static $logAttributes = ['key' , 'value'];
    protected static $logOnlyDirty = true;
    public function getDescriptionForEvent(string $eventName): string
    {
        return "User meta has been {$eventName}";
    }


    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
