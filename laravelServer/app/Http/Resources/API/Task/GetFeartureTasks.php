<?php

namespace App\Http\Resources\API\Task;

use Illuminate\Http\Resources\Json\JsonResource;

class GetFeartureTasks extends JsonResource
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
          'title'=>$this->title,
          'description'=>$this->description,
          'status'=>$this->status,
          'contract_id'=>$this->contract_id,
          'site_link'=>$this->site_link,
          'theme_link'=>$this->theme_link,
          'feature'=>$this->feature,
        ];
    }
}
