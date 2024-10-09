<?php

namespace App\Http\Resources\API\User;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class Todo extends JsonResource
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
            'id'        => $this->id,
            'created_at'   => Verta::instance($this->created_at)->format('Y-n-j'),
            'read_at'   => $this->read_at,
            'type'      => $this->type,
            'data'      => $this->data
        ];
    }
}
