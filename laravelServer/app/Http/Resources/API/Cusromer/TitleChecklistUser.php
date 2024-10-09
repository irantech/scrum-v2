<?php

namespace App\Http\Resources\API\Cusromer;

use Illuminate\Http\Resources\Json\JsonResource;

class TitleChecklistUser extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $section = \App\Models\Section::find($this->section_id);
        return [
            'id'        =>  $this->id ,
            'section'   =>  new Section($section),
            'average'   =>  $this->average
        ];
    }
}
