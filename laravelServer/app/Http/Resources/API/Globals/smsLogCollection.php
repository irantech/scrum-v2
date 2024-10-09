<?php

namespace App\Http\Resources\API\Globals;

use Illuminate\Http\Resources\Json\ResourceCollection;

class smsLogCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
