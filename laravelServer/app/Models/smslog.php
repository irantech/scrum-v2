<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class smslog extends Model
{
    protected $fillable = ['sms_text'  , 'status' , 'phone_number' , 'title'];

    protected $table = 'sms_logs';

    public function customers() {
        return $this->belongsTo(Customer::class);
    }
}
