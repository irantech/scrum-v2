<?php

namespace App\Http\Resources\API\Contract;

use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
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
            'name'              => $this->name ,
            'email'             => $this->email,
            'old_id_customer'   => $this->old_id_customer
        ];
    }
}
