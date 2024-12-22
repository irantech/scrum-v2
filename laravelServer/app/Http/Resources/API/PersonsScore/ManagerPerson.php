<?php

namespace App\Http\Resources\API\PersonsScore;

use App\Http\Resources\API\Contract\reverseChecklist;
use App\Http\Resources\API\Contract\reverseContract;
use App\Models\Checklist;
use App\Models\ChecklistContract;
use App\Models\Contract;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerPerson extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $checklist_contract=ChecklistContract::find($this->checklist_contract_id);
        $checklist=Checklist::find($checklist_contract->checklist_id);
        $contract=Contract::find($checklist_contract->contract_id);
        return [
            'checklist_contract_id'=>$this->checklist_contract_id,
            'checklist'        => new reverseChecklist($checklist),
            'contract'         => new reverseContract($contract),
            'sum_manager_negative_score_checklist'=>$this->sum_manager_negative_score,
            'sum_manager_like_score_checklist'=>$this->sum_manager_like_score,
        ];
    }
}
