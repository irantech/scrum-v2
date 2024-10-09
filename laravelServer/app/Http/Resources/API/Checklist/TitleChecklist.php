<?php

namespace App\Http\Resources\API\Checklist;

use App\Http\Resources\API\Globals\SectionCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class TitleChecklist extends JsonResource
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
            'title'         => $this->title,
            'description'   => $this->description,
            'section'       => new SectionCollection($this->sections)
        ];
    }
}
