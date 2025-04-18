<?php

namespace App\Http\Resources\API\Task;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'avatar'        => $this->avatar(),
        ];
    }
}
