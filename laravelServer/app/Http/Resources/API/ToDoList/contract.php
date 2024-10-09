<?php

namespace App\Http\Resources\API\ToDoList;

use Illuminate\Http\Resources\Json\JsonResource;

class contract extends JsonResource
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
            'id'               => $this->id,
            'title'            => $this->title ,
            'contract_code'    => $this->contract_id ,
            'sign_date'	       => $this->sign_date ,
            'end_date'         => $this->end_date ,
            'start_date'       => $this->start_date,
        ];
    }
}
