<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    protected $table = 'softwares';

    public function progress()
    {
        return $this->hasMany(BaseProgress::class);
    }
}
