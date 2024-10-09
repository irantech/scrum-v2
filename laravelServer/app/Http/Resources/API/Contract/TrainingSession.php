<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\TrainigSession\ContractChecklist;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\API\User\User ;

class TrainingSession extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $contract_checklist = \App\Models\ChecklistContract::find($this->checklist_contract_id);

        return [
            'id'                    => $this->id  ,
            'user'                  => new User($this->user),
            'checklist_contract'    => new ContractChecklist($contract_checklist) ,
            'session_date'          => $this->session_date ,
            'session_time'          => $this->session_time,
            'location_status'       => $this->location_status ? 'in_person' : 'online',
            'location_place'        => $this->location_place  ? 'in' : 'out',
            'address'               => $this->address ,
            'status'                => $this->status ,
            'duration'              => $this->duration,
            'cancel_reason'         => $this->cancel_reason ,
            'contributors'          => $this->contributors
        ];
    }
}
