<?php

namespace App\Http\Resources\API\PersonScore;

use App\Http\Resources\API\Contract\reverseChecklist;
use App\Http\Resources\API\Contract\reverseContract;
use App\Models\ChecklistContract;
use Illuminate\Http\Resources\Json\JsonResource;

class Details extends JsonResource
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
        $checklist = \App\Models\Checklist::find($checklist_contract->checklist_id);
        $contract = \App\Models\Contract::find($checklist_contract->contract_id);
        return [
            'checklist_contract_id'=>$this->checklist_contract_id,
            'checklist'        => new reverseChecklist($checklist),
            'contract'         => new reverseContract($contract),
            'sum_user_negative_score' => $this->sum_user_negative_score,
            'sum_user_positive_score' => $this->sum_user_positive_score

        ];
    }
}
