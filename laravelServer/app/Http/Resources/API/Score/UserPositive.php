<?php

namespace App\Http\Resources\API\Score;

use App\Http\Resources\API\Globals\Section;
use App\Http\Resources\API\User\Role;
use App\Http\Resources\API\User\User as User_resource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPositive extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
