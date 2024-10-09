<?php

namespace App\Http\Resources\API\ToDoList;

use Illuminate\Http\Resources\Json\JsonResource;

class checklist extends JsonResource
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
            'title'             => $this->title,
        ];
    }
}
