<?php

namespace App\Http\Resources\API\Contract;

use Illuminate\Http\Resources\Json\JsonResource;

class reverseSection extends JsonResource
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
            'id'      =>  $this->section->id,
            'title'      =>  $this->section->title,
        ];
    }
}
