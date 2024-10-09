<?php

namespace App\Http\Resources\API\TrainigSession;

use App\Http\Resources\API\ToDoList\checklist as checklistResource;
use App\Http\Resources\API\Contract\Contract as contractResource;
use App\Models\Checklist;
use App\Models\Contract;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractChecklist extends JsonResource
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
            'id'            =>  $this->id ,
            'checklist'     => new checklistResource(Checklist::find($this->checklist_id)) ,
            'contract'      => new contractResource(Contract::find($this->contract_id))
        ];
    }
}
