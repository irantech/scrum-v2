<?php

namespace App\Http\Resources\API\User;

use App\Http\Resources\API\Globals\Section;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'role'      =>  new Role($this->getRole()) ,
            'section'   =>  new Section($this->section),
            'signature' => $this->signature(),
            'trashed'   =>  !$this->active,
            'avatar'   => $this->avatar(),
        ];
    }
}
