<?php

namespace App\Http\Resources\API\Score;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserScoreCollection extends ResourceCollection
{
    protected $userNegativeScoreSum;

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
