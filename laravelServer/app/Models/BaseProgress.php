<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseProgress extends Model
{
	use SoftDeletes,LogsActivity;

    protected static $logAttributes = ['section_id' , 'software_id' , 'user_role','title','description','private_description','percentage'];
    protected static $logName = 'base_progress_activities';
    protected static $logOnlyDirty = true;
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This base progress has been {$eventName}";
    }

	public function progress()
	{
		return $this->hasMany(SubProgress::class)->withTrashed();
	}
	public function contract()
	{
		return $this->belongsToMany(Contract::class, 'contract_progress', 'base_progress_id', 'contract_id');
	}

    public function ancillary()
    {
        return $this->belongsToMany(Ancillary::class, 'ancillary_progress','base_progress_id','ancillary_id');
    }

    public function child_progress()
    {
        return $this->hasMany(ContractType::class)->where('base_progress_id',$this->base_progress_id);
    }
    public function contract_progress()
    {
        return $this->hasMany(ContractProgress::class);
    }

    public function ancillary_sub_progress()
    {
        return $this->hasMany(AncillaryProgress::class)->where('base_progress_id',$this->base_progress_id);
    }

    public function ancillary_progress()
    {
        return $this->hasMany(AncillaryProgress::class);
    }

    public function software()
    {
        return $this->belongsTo(Software::class);
    }



    public function scopeIdDescending($query)
    {
        return $query->orderBy('id','DESC');
    }

}
