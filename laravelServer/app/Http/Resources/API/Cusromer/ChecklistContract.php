<?php

namespace App\Http\Resources\API\Cusromer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ChecklistContract extends JsonResource
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
            'id'            => $this->id ,
            'checklist'     => new Checklist($this->checklist),
            'titleChecklistUser'  => new TitleChecklistUserCollection($this->titleChecklistUserAverage),
        ];
    }
}
