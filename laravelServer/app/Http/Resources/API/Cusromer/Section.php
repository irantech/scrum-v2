<?php

namespace App\Http\Resources\API\Cusromer;

use Illuminate\Http\Resources\Json\JsonResource;

class Section extends JsonResource
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
            'title'         => $this->title ,
            'color'         => $this->color ,
            'order'         => (int)$this->order
        ];
    }
}
