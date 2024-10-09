<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    protected $fillable = ['title'];

    public function roles()
    {
        return $this->belongsToMany(role::class);
    }

    public function getPredifinedStatus() {
        if($this->pre_defined == 1)
            return true ;
        return false;
    }
}
