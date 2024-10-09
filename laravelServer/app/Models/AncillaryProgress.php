<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AncillaryProgress extends Model
{


    public function base_progress()
    {
        return $this->hasMany(BaseProgress::class);
    }



}
