<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\Globals\Section;
use Illuminate\Http\Resources\Json\JsonResource;

class sumProceess extends JsonResource
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
            'section'     => new Section($this->section),
            'sumTime'     => $this->sum ? $this->sum : 0
        ];
    }
}
