<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractProgress extends Model
{
    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }
    public function base_progress()
    {
        return $this->hasMany(BaseProgress::class);
    }
}
