<?php

namespace App\Http\Resources\API\ContractChecklist;

use App\Http\Resources\API\Globals\Section;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class signChecklists extends JsonResource
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
            'id'                  => $this->id,
            'section'             => new Section($this->section) ,
            'status'              => (int)$this->status,
            'user'                => new User($this->user) ,
            'created_at'          => Verta::instance($this->created_at)->format((' j %B Y ')) ,
            'checklist_contract'  => $this->checklist_contract_id
        ];
    }
}
