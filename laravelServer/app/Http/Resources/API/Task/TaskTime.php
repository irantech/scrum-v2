<?php

namespace App\Http\Resources\API\Task;

use App\Http\Resources\API\Globals\Section;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskTime extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'                     => $this->id ,
            'section'                => new Section($this->section) ,
            'task_status'            => $this->task_status ,
            'task_time_duration'     => $this->task_time_duration == '00:00:00' ? 0 :  $this->task_time_duration,
            'task_day_duration'      => $this->task_day_duration  ,
            'interval_time_duration' => $this->interval_time_duration  == '00:00:00' ? 0 :$this->interval_time_duration ,
            'interval_day_duration'  =>$this->interval_day_duration  ,
        ];
    }
}
