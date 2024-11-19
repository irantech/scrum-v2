<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Meeting extends Model
{
    protected $guarded=[];

    public function meetingDetails()
    {
        return $this->hasMany(MeetingDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
