<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\Checklist\TitleChecklist as TitleChecklistResource;
use App\Http\Resources\API\Globals\Section as sectionResource;
use App\Http\Resources\API\User\User as UserResource;
use App\Models\Section;
use App\Models\TitleChecklist;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class userChecklist extends JsonResource
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
            'user'              => new UserResource(User::withTrashed()->find($this->user_id)),
            'titleChecklists'   => new TitleChecklistResource(TitleChecklist::find($this->titleChecklist_id)) ,
            'status'            => (int)$this->status ,
            'section'           => new sectionResource(Section::find($this->section_id))
        ];
    }
}
