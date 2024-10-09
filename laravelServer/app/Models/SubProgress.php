<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SubProgress extends Model
{
    use SoftDeletes,LogsActivity;
    protected static $logAttributes = ['section_id' , 'base_progress_id' , 'title','description'];
    protected static $logName = 'sub_progress_activities';
    protected static $logOnlyDirty = true;
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This sub progress has been {$eventName}";
    }

    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function base_progress()
    {
        return $this->belongsTo(BaseProgress::class)->orderBy('id','desc');
    }

    public function ancillaries()
    {
        return $this->belongsToMany(Ancillary::class)->withPivot('ancillary_id','status','estimated_time','refer_to','start_date');
    }

    public function contracts(){
        return $this->belongsToMany(Contract::class)
            ->withPivot('contract_id','status','estimated_time','refer_to','start_date');
    }
}
