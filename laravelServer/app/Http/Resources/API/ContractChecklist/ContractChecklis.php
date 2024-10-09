<?php

namespace App\Http\Resources\API\ContractChecklist;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractChecklis extends JsonResource
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
            'id'    =>  $this->id ,
            'checklist'  => new Checklist($this->checklist_id)
        ];
    }
}
