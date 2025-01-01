<?php

namespace App\Http\Resources\API\Score;

use App\Http\Resources\API\Contract\reverseChecklist;
use App\Http\Resources\API\Contract\reverseContract;
use Illuminate\Http\Resources\Json\JsonResource;

class Score extends JsonResource
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
            'id'               => $this['id'],
            'users'            => $this['users'],
            'checklist'        => new reverseChecklist($this['checklist']),
            'contract'         => new reverseContract($this['contract']),
            'todolist'         => new \App\Http\Resources\API\Contract\ToDoListCollection($this['todolist']),
            'process_reversed_count'    => $this['process_reversed_count'],
            'reverse_data'              => $this['reverse_data'],
            'negative_score'     =>$this['manager_user_negative'],
            'positive_score'     =>$this['manager_user_positive']
        ];
    }
}
