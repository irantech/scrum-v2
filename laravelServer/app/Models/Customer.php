<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class,'old_id_customer','old_id_customer');
    }

    public function smsLogs(){
        return $this->hasMany(smslog::class);
    }
}
