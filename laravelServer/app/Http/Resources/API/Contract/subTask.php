<?php

namespace App\Http\Resources\API\Contract;


use App\Http\Resources\API\ToDoList\todoListCollection;
use App\Http\Resources\API\User\UserCollection;
use Illuminate\Http\Resources\Json\JsonResource;


class subTask extends JsonResource
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
        $users =  $this->titleChecklistUser()->groupBy('user_id')->pluck('user_id');
        $users = \App\Models\User::whereIn('id' , $users)->get();

        $todoList = $this->todoList()->where('status' , '!=' , 'stop')->get() ;
        return [
            'id'               => $this->id,
            'users'            => new UserCollection($users),
            'checklist'        => new reverseChecklist($checklist),
            'contract'         => new reverseContract($contract),
            'todolist'         => new \App\Http\Resources\API\Contract\ToDoListCollection($todoList),
            'process_reversed_count'   => $this->sub_task_process_count,
            'reverse_data'     => new reverseProcessCollection($this->subTaskProcess),
        ];
    }



}
