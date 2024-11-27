<?php

namespace App\Http\Resources\API\Question;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionStore extends JsonResource
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
            'id'=>$this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'answer_time'=>$this->answer_time,
            'answer_description'=>$this->answer_description,
            'user_id'=>$this->user_id,
            'section_id'=>$this->section_id
        ];
    }
}
