<?php

namespace App\Http\Resources\API\ToDoList;

use App\Http\Resources\API\User\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class userTodoList extends JsonResource
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
            'id'            => $this->id ,
            'name'          => $this->username ,
            'fullName'      => $this->name,
            'role'          =>  new Role($this->getRole()) ,
            'todo_lists'    => new todoListCollection($this->todoLists)
        ];
    }
}
