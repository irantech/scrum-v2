<?php

namespace App\Http\Resources\API\Score;

use Illuminate\Http\Resources\Json\JsonResource;

class ManagerScorePositive extends JsonResource
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
            'id'        =>  $this->id,
            'name'      =>  $this->name,
            'userName'  =>  $this->username,
            'email'     =>  $this->email,
            'signature' => $this->signature(),
            'manager_positive_score' => 5,
        ];    }
}