<?php

namespace App\Http\Resources\API\ToDoList;

use App\Http\Resources\API\Globals\Section;
use \App\Http\Resources\API\Checklist\Checklist;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class taskTime extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $task_class = $this->tasktimeable_type;

        $task_model = new $task_class ;

        $task = $task_model->where('id' , $this->tasktimeable_id)->first();

        $class_parts = explode('\\', $task_class);
        $base_class_model = end($class_parts);
        $base_class = 'App\Http\Resources\API\ToDoList\\'.$base_class_model;
        return [
            'id'                => $this->id ,
            'section'           => new Section($this->section) ,
            'checklist'         => new $base_class($task) ,
            'task_time_duration'     =>  Verta::instance($this->task_time_duration)->format(('H:i')) ,
            'interval_time_duration'     =>  Verta::instance($this->interval_time_duration)->format(('H:i')) ,
            'interval_day_duration'     => $this->interval_day_duration ,
            'task_day_duration'     => $this->task_day_duration ,
            'task_status'            => $this->task_status ,
            'description'       => $this->description
        ];
    }
}
