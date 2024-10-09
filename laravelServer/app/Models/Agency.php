<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = ['irantech_agency_id','manager_name' , 'agency', 'type'];

    public function AgencyInfo(){
        return $this->hasMany(AgencyInfo::class);
    }
}
