<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyInfo extends Model
{

    protected $fillable = ['agency_id', 'row_id', 'label', 'info'];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
