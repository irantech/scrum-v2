<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Capability extends Model
{
    use SoftDeletes;

    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }
}
