<?php

namespace App\Http\Resources\API\Auth;

use App\Http\Resources\API\Auth\Role;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class user extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name ,
            'userName'      => $this->username ,
            'email'         => $this->email ,
            'role'          => new Role($this->getRole()),
            'mobile'        => $this->phone_number,
            'avatar'        => $this->avatar(),
            'signature'     => $this->signature(),
            'tasks'         => $this->todoListsCount(),
            'trainingSessionCount'     =>( $this->trainingSessionCount() + $this->NotSetTrainingSessionForContract() ) ,
            'request_count' =>  Auth::user()->tokenCan('manager-show-requests') ? $this->managerRequestsCount() : ''
        ];
//         'unread_notify'  => $this->unread()
    }
}
