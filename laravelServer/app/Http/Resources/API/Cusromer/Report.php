<?php

namespace App\Http\Resources\API\Cusromer;

use Illuminate\Http\Resources\Json\JsonResource;

class Report extends JsonResource
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
            'id'      =>  $this->id ,
            'name'    =>  $this->name ,
            'phone_number'  => $this->phone_number,
            'contracts'     => new ContractCollection($this->contracts)
        ];
    }
}
