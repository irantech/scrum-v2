<?php

namespace App\Http\Resources\API\Activity;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\ResourceCollection;

class acrivityCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  $this->collection->map(function ($item){
            return[
                'id' => $item->id,
                'user' => $item->causer->username,
                'description' => $item->description,
                'log_name'    => $item->log_name ,
                'created_at'  => Verta::instance($item->created_at)->format('Y-n-j')
            ];
        });
    }
}
