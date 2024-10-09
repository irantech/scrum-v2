<?php

namespace App\Http\Resources\API\Task;

use App\Http\Resources\API\Task\subCollection;
use App\Http\Resources\API\Task\User;
use App\Http\Resources\API\Globals\Section;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class sub extends JsonResource
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
            'id'                => $this->id ,
            'body'              => $this->description ,
            'status'            => $this->status ,
            'checklist_process' => $this->checklist_process_id ,
            'file_list'         => $this->file_list ,
            'date'              => Verta::instance($this->created_at)->formatJalaliDatetime(),
            'user'              => new User($this->user),
            'assigned'          => $this->assigned ? new User($this->assignedUser) : null,
            'parent'            => $this->parent_id,
            'order'             => $this->order,
            'seen'              => $this->seen,
            'section'           => new Section($this->section),
            'replies'           => new subCollection($this->replies)
        ];
    }
}
