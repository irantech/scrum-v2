<?php

namespace App\Http\Resources\API\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\Globals\Section;

class Role extends JsonResource
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
            'id'            => $this->id,
            'title'         => $this->title,
            'type'          => $this->type ,
            'section'       => new Section($this->section),
            'trashed'       => $this->trashed()
        ];
    }
}
