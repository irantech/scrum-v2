<?php

namespace App\Http\Resources\API\Task;


use App\Http\Resources\API\Task\subCollection;
use App\Http\Resources\API\Globals\Section;
use App\Http\Resources\API\User\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class assigned extends JsonResource
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
            'id'        =>  $this->id,
            'name'      =>  $this->name ,
            'userName'  =>  $this->username,
            'subTask'   => new subCollection($this->taskSubTasks)
        ];
    }

}
