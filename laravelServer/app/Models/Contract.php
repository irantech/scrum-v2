<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\Traits\LogsActivity;

class Contract extends Model {
    use SoftDeletes , LogsActivity;

   protected $fillable = ['customer' , 'type' , 'title' ,'sign_date' , 'start_date', 'end_date' , 'description' , 'code' , 'progress'];

    protected static $logAttributes = ['customer' , 'type' , 'title' ,'sign_date' , 'start_date', 'end_date' , 'description' , 'code' , 'progress'];
    protected static $logName = 'contract_activities';
    protected static $logOnlyDirty = true;
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This contract has been {$eventName}";
    }


    public function scopeIdDescending( $query ) {
        return $query->orderBy( 'id', 'DESC' );
    }

    public function type() {
        return $this->belongsTo( ContractType::class, 'type_id' );
    }

    public function customer() {
        return $this->belongsTo( Customer::class, 'old_id_customer', 'old_id_customer' );
    }

    public function sub_progress() {
        return $this->belongsToMany( SubProgress::class )
                    ->withPivot( 'sub_progress_id', 'status', 'estimated_time', 'refer_to', 'start_date' )
                    ->withTimestamps();
    }

    public function ancillary() {
        return $this->hasMany( Ancillary::class);
    }

    /*This is for base progresses */
//    i think this rel is extra
    public function base_progress() {
        return $this->belongsToMany( BaseProgress::class, 'contract_progress' )->withPivot( 'status' );
//            ->withTimestamps();
    }

    public function getSignDateAttribute( $value ) {
        if ( App::getLocale() == 'fa_IR' ) {
            return Verta::instance( $value )->formatDate();
        }

        return $value;
    }

    public function getStartDateAttribute( $value ) {
        if ( App::getLocale() == 'fa_IR' ) {
            return Verta::instance( $value )->formatDate();
        }

        return $value;
    }

    public function getEndDateAttribute( $value ) {
        if ( App::getLocale() == 'fa_IR' ) {
            return Verta::instance( $value )->formatDate();
        }

        return $value;
    }
    public function checklists() {
        return $this->belongsToMany(Checklist::class , 'checklist_contract' , 'contract_id' , 'checklist_id')
            ;
    }

    public function checklistContract() {
        return $this->hasMany(ChecklistContract::class);
    }
    public function tasks(){
        return $this->hasMany(Task::class);
    }
}
