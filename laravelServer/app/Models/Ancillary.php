<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\Traits\LogsActivity;

class Ancillary extends Model
{

    use LogsActivity ;
    protected $fillable = ['title' , 'contract_code' , 'contract_id'];

    protected static $logAttributes = ['title' , 'contract_code' , 'contract_id'];
    protected static $logName = 'ancillary_activities';
    protected static $logOnlyDirty = true;
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This ancillary has been {$eventName}";
    }

//    use SoftDeletes;

    public function base_progress()
    {
        return $this->belongsToMany(BaseProgress::class, 'ancillary_progress')
            ->withPivot('status')
//            ->withTimestamps()
        ;
    }


    public function sub_progress() {
        return $this->belongsToMany( SubProgress::class )
            ->withPivot( 'sub_progress_id', 'status', 'estimated_time', 'refer_to', 'start_date' )
            ->withTimestamps();
    }


    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }


}
