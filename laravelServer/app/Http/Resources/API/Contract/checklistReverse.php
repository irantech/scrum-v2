<?php

namespace App\Http\Resources\API\Contract;


use App\Http\Resources\API\ToDoList\todoListCollection;
use Illuminate\Http\Resources\Json\JsonResource;


class checklistReverse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {

        $checklist = \App\Models\Checklist::find($this->checklist_id);
        $contract = \App\Models\Contract::find($this->contract_id);
        return [
            'id'               => $this->id,
            'checklist'        => new reverseChecklist($checklist),
            'contract'         => new reverseContract($contract),
            'todolist'         => new \App\Http\Resources\API\Contract\ToDoListCollection($this->todoList),
            'process_reversed_count'   => $this->reversed_checklist_process_count,
            'reverse_data'     => new reverseProcessCollection($this->reversedChecklistProcess),
        ];
    }
}
