<?php

namespace App\Http\Resources\API\Globals;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class holiday extends JsonResource
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
            'id'        => $this->id ,
            'title'     => $this->title ,
            'date'      => Verta::instance($this->date)->formatDate()
        ];
    }
}
