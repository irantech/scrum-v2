<?php

namespace App\Http\Resources\API\Task;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class todoList extends JsonResource
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
            'id'                => $this->id ,
            'title'             => $this->title ,
            'user'              => new \App\Http\Resources\API\Task\User($this->user) ,
            'description'       => $this->description ,
            'status'            => $this->status ,
            'todo_status'       => $this->todo_status ,
            'difference_time'   => $this->difference_time ,
        ];
    }
}
