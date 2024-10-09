<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\Checklist\Checklist;
use App\Http\Resources\API\ContractChecklist\subTaskCollection;
use App\Http\Resources\API\Globals\Section;
use App\Http\Resources\API\User\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class checklistProcess extends JsonResource
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
            'id' => $this->id,
            'section' => new Section($this->section),
            'user' => new User($this->user),
            'checklist' => new Checklist($this->checklist),
            'contract' => $this->contract_id,
            'status' => (int)$this->status,
            'description' => $this->description,
            'duration' => $this->duration,
            'date' => Verta::instance($this->created_at)->format((' j %B Y ')),
            'error_count' => $this->checklist_reverses_error_count ,
            'offer_count' => $this->checklist_reverses_offer_count ,
            'reverse_data' => new subTaskCollection($this->subtasks)
        ];
    }
}
