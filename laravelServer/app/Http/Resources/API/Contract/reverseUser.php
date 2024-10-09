<?php

namespace App\Http\Resources\API\Contract;

use Illuminate\Http\Resources\Json\JsonResource;

class reverseUser extends JsonResource
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
            'id'        =>  $this->id,
            'name'      =>  $this->name ,
            'userName'  =>  $this->username,
            'email'     =>  $this->email,
        ];
    }
}
