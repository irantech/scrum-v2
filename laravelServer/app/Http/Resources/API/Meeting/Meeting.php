<?php

namespace App\Http\Resources\API\Meeting;

use App\Http\Resources\API\User\User;
use Illuminate\Http\Resources\Json\JsonResource;

class Meeting extends JsonResource
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
            'user_id' => $this->user_id,
            'meeting_with_superadmin' => $this->meeting_with_superadmin,
            'subject' => $this->subject,
            'description' => $this->description,
            'result' => $this->result,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new User($this->user),
            'meeting_detail' => $this->meetingDetails->map(function ($detail) {
                return new MeetingDetail($detail);
            }),
            ];
    }
}
