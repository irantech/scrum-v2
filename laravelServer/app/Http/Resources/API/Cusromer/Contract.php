<?php

namespace App\Http\Resources\API\Cusromer;

use Illuminate\Http\Resources\Json\JsonResource;

class Contract extends JsonResource
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
            'user_id'           => $this->user_id,
            'type_id'           => $this->type_id,
            'old_id_customer'   => $this->old_id_customer,
            'contract_code'     => $this->contract_code,
            'title'             => $this->title,
            'sign_date'         => $this->sign_date,
            'start_date'        => $this->start_date,
            'checklistContract' => new ChecklistContractCollection($this->checklistContract)
        ];
    }
}
