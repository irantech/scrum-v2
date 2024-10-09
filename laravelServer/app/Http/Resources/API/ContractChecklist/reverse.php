<?php

namespace App\Http\Resources\API\ContractChecklist;

use App\Http\Resources\API\Globals\Section;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class reverse extends JsonResource
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
            'parent'            => $this->parent_id,
            'order'             => $this->order,
            'seen'              => $this->seen,
            'section'           => new Section($this->section),
            'accept_count'      => $this->accept_count ? $this->accept_count : 0,
            'reject_count'      => $this->reject_count ? $this->reject_count : 0,
            'replies'           => new reverseCollection($this->replies)
        ];
    }
}
