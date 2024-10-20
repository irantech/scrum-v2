<?php

namespace App\Http\Resources\API\MeetingDetails;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\User\User;

class MeetingDetails extends JsonResource
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
            'id' => $this->id,
            'meeting_id' => $this->meeting_id,
            'user_id' => $this->user_id,
            'approval_status' => $this->approval_status,
            'description' => $this->description,
            'date_time' => $this->date_time,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new User($this->user),
            'meeting' => new Meeting($this->meeting),
        ];
    }
}
