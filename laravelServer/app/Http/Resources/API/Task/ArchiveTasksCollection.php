<?php

namespace App\Http\Resources\API\Task;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArchiveTasksCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
