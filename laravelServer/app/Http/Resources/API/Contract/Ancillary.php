<?php

namespace App\Http\Resources\API\Contract;

use Illuminate\Http\Resources\Json\JsonResource;

class Ancillary extends JsonResource
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
            'contract_id'   => $this->contract_id,
            'contract_code' => $this->contract_code ,
            'title'         => $this->title
        ];
    }
}
