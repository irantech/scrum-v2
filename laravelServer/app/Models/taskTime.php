<?php

namespace App\Models;

use App\Models\Checklist;
use App\Models\ChecklistContract;
use App\Models\Section;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class taskTime extends Model
{
    use SoftDeletes , Timestamp;
    protected $table = 'task_times';

    protected $fillable =[
        'section_id' ,
        'checklist_id' ,
        'task_status' ,
        'task_day_duration' ,
        'task_time_duration' ,
        'interval_day_duration' ,
        'interval_time_duration' ,

    ];

    public  function section(){
        return $this->belongsTo(Section::class);
    }

    public function checklist() {
        return $this->morphedByMany(Checklist::class, 'tasktimeable');
    }

    public function tasktimeable()
    {
        return $this->morphTo();
    }
}
