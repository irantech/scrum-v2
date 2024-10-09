<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\Globals\Section;
use Illuminate\Http\Resources\Json\JsonResource;
use function _HumbugBox7eb78fbcc73e\bar;

class ChecklistContract extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $proccess  = $this->checklistProcess()->orderBy('created_at' , 'desc')->first() ;
        return [
            'id'        => $this->id,
            'checklist' => new Checklist($this->checklist),
            'section'   => $proccess ? new Section(\App\Models\Section::find($proccess->section_id)) : '',
            'status'    => $proccess ? (int)$proccess->status : '',
        ];
    }
}
