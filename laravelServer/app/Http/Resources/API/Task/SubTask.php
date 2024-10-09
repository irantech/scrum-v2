<?php

namespace App\Http\Resources\API\Task;

use App\Models\ToDoList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SubTask extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $delivery_time_difference = 0 ;
        if($this->status == 'complete') {
           if(count($this->lastTodoList) > 0) {
               $ending_time = $this->lastTodoList[0]->ending_time ;
               if(!$ending_time) {
                   $ending_time = Carbon::now();
               }
               $delivery_time = Carbon::parse($this->delivery_time);
               $delivery_time_difference = $ending_time->diffInDays($this->delivery_time);
           }

        }

        $task_id = $this->id;
        $user = new User() ;
        $user_exist = [] ;
        if($this->subtasks) {
            $user_exist = $this->subtasks->pluck('assigned');
        }


        $userList = $user->with(['subTaskError' => function ($query) use ($task_id) {
            $query->where('subtaskable_id' , $task_id);
        },'subTaskOffer' => function ($query) use ($task_id) {
            $query->where('subtaskable_id' , $task_id);
        },'subTaskPeriodic' => function ($query) use ($task_id) {
            $query->where('subtaskable_id' , $task_id);
        }])->whereHas('taskSubTasks' , function ($request) use($task_id) {
            $request->where('subtaskable_id' , $task_id);
        })->whereIn('id' , $user_exist)->get();

        $todolist = [] ;
        if($todolist) {
            $todolist = $this->todoList ;
        }

        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'delivery_delay'   => $delivery_time_difference,
            'todolist'         => new \App\Http\Resources\API\Contract\ToDoListCollection($todolist),
            'reverse_data'     => new detailCollection($userList),
        ];
    }
}
