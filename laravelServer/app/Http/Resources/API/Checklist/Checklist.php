<?php

namespace App\Http\Resources\API\Checklist;

use Illuminate\Http\Resources\Json\JsonResource;

class Checklist extends JsonResource
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
            'description'       => $this->description ,
            'sections'          => json_decode($this->sections),
            'title_checklists'  => new TitleChecklistCollection($this->titleChecklists),
            'language'          => new Language($this->language),
            'trashed'           => $this->trashed()
        ];
    }
}
