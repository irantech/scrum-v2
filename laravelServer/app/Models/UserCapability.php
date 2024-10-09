<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCapability extends Model
{
    use SoftDeletes;

    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
