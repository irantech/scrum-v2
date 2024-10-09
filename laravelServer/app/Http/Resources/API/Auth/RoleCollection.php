<?php

namespace App\Http\Resources\API\Auth;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\API\Globals\Section;

class RoleCollection extends ResourceCollection
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
            return [
                'id'            => $item->id,
                'title'         => $item->title,
                'type'          => $item->type ,
                'section'       => new Section($item->section),
                'trashed'       => $item->trashed()
            ];
        });

    }
}
