<?php

namespace App\Http\Resources\API\Meeting;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingDetail extends JsonResource
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
            'user_id'      =>  $this->user_id,
            'meeting_with_superadmin'  =>  $this->meeting_with_superadmin,
            'subject'     =>  $this->subject,
            'description'   =>  !$this->description,
            'result'   => $this->result,
            'deleted_at'   => $this->deleted_at,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
