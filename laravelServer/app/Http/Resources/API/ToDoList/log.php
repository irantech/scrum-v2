<?php

namespace App\Http\Resources\API\ToDoList;

use App\Http\Resources\API\User\User;
use Illuminate\Http\Resources\Json\JsonResource;

class log extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = \App\Models\User::find($this->causer_id)  ;
        $todo = \App\Models\ToDoList::find($this->subject_id);
        return [
            'id'          => $this->id ,
            'causer'      => new User($user) ,
            'todoList'    => new todoList($todo) ,
            'properties'  => $this->properties ,


        ];
    }
}
