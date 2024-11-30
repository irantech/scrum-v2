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
        list($hours, $minutes, $seconds) = explode(':', $this->sum);

        $days = (int)(floor($hours / 24));
        $remainingHours = $hours % 24;
        return [
//            'section'     => new Section($this->section),
            'section'     => [
                                "id"=>$this->section_id,
                                "title"=> $this->title,
                                "order"=> $this->order,
                                "color"=> $this->color
                                ],
            'sumTime'     => $this->sum ? $days ." days "."( ".$remainingHours." : ".$minutes." : ".$seconds." )": 0
        ];
    }
}


