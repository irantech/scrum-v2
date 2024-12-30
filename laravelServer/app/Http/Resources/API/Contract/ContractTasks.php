<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\Task\TaskCollection;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractTasks extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'id'               => $this->id,
            'user_id'	       => $this->user_id ,
            'old_id_customer'  => $this->old_id_customer,
            'title'            => $this->title ,
            'contract_code'    => $this->contract_code ,
            'sign_date'	       => $this->sign_date ,
            'end_date'         => $this->end_date ,
            'start_date'       => $this->start_date,
            'customer'         => new Customer($this->customer),
            'tasks'             =>new TaskCollection($this->tasks),
            'ancillary'        => new AncillaryCollection($this->ancillary) ,
            'jalali_created_at'=> Verta::instance($this->created_at)->formatDatetime(),
            'jalali_sign_date' => Verta::instance($this->sign_date)->formatDate(),
            'jalali_start_date'=>Verta::instance($this->start_date)->formatDate(),
            'jalali_end_date'  => Verta::instance($this->end_date)->formatDate(),
            'checklistContract'=> new ChecklistContractCollection($this->checklistContract)
        ];
    }
}
