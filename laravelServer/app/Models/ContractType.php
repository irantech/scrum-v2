<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractType extends Model
{
    use SoftDeletes;

    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function contract()
    {
        return $this->hasMany(Contract::class);
    }

    public function isDeleted()
    {
        return $this->trashed();
    }
}
