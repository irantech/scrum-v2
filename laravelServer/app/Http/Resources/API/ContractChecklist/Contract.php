<?php

namespace App\Http\Resources\API\ContractChecklist;

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
            'id'               =>  $this->id ,
            'title'            =>  $this->title ,
            'contract_code'    =>  $this->contract_code ,
            'customer'         =>  $this->customer->name ,
            'customer_address' =>  $this->customer->address ,
            'sign_date'        =>  $this->sign_date  ,
             'start_date'      =>  $this->start_date ,
            'end_date'         =>  $this->end_date ,
            'domain_link'      =>  $this->domain_link ,
            'theme_link'       =>  $this->theme_link ,
        ];
    }
}
