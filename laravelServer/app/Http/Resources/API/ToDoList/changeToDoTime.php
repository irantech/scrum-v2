<?php

namespace App\Http\Resources\API\ToDoList;

use App\Http\Controllers\API\ToDoList\ToDoListController;
use App\Http\Resources\API\User\User;
use Illuminate\Http\Resources\Json\JsonResource;

class changeToDoTime extends JsonResource
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
            'id'                    => $this->id ,
            'reason'                => $this->reason,
            'starting_time'         => $this->starting_time,
            'ending_time'           => $this->ending_time,
            'has_confirmed'         => $this->has_confirmed,
            'user'                  => new User($this->user),
            'todo_list'             => new ToDoListController($this->todolist),
        ];
    }
}
