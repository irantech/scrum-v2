<?php

namespace App\Http\Resources\API\Score;

use App\Http\Resources\API\Globals\Section;
use App\Http\Resources\API\User\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class UserScore extends JsonResource
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
            'name'      =>  $this->name,
            'userName'  =>  $this->username,
            'email'     =>  $this->email,
            'signature' => $this->signature(),
            'negative_score_sum' => $this->additional['negative_score_sum'],
        ];    }
}
