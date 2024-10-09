<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\ToDoList\todoListCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class reverseContract extends JsonResource
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
            'id'               => $this->id,
            'user_id'	       => $this->user_id ,
            'title'            => $this->title ,
            'customer'         => new Customer($this->customer),

        ];
    }
}
