<?php

namespace App\Http\Resources\API\ToDoList;

use App\Http\Resources\API\ContractChecklist\subTaskCollection;
use App\Http\Resources\API\Task\assignedCollection;
use App\Http\Resources\API\ToDoList\contract;
use App\Http\Resources\API\Task\TaskLabelCollection;
use App\Http\Resources\API\User\User;
use App\Http\Resources\API\User\UserCollection;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class Task extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $todo_user = $this->todoList()->groupBy('user_id')->pluck('user_id');
        $todo_user = \App\Models\User::whereIn('id' , $todo_user)->get();
        $totalTimeDuration = $this->totalTimeDuration($this->taskTimes) ;
//        $task_id = $this->id;
//        $user = new \App\Models\User() ;
//        $userList = $user->with(['taskSubTasks'  => function($query) use ($task_id) {
//            $query->where('subtaskable_id' , $task_id);
//        }])->whereHas('taskSubTasks' , function ($request) use($task_id) {
//            $request->where('subtaskable_id' , $task_id);
//        })->get();
        return [
            'id'            => $this->id ,
            'title'         => $this->title ,
            'description'   => $this->description ,
            'user_owner'    => new User($this->user) ,
            'contract'       => new contract($this->contract) ,
            'status'         => $this->status ,
            'site_link'      => $this->site_link ,
            'theme_link'     => $this->theme_link ,
            'label_list'     => new TaskLabelCollection($this->taskLabels),
//            'sub_task'       => new assignedCollection($userList),
//            'not_assigned_sub_task'   => new subTaskCollection($this->notAssigendsubtasks),
//            'task_time'      => new \App\Http\Resources\API\Task\TaskTimeCollection($this->taskTimes),
            'user_list'      => new UserCollection($todo_user),
            'delivery_time'  => $this->delivery_time ? Verta::instance($this->delivery_time)->format('Y/M/d') : '',
            'delivery_time_base' => $this->delivery_time,
            'total_task_day' => $totalTimeDuration['days'],
            'total_task_time'=> $totalTimeDuration['time'],
            'created_at'     => Verta::instance($this->created_at)->format('Y-m-d')
        ];
    }
    private function totalTimeDuration($taskTimes) {
        if( count($taskTimes) > 0 ){
        $total_time = 0 ;
        foreach ($taskTimes as $key => $taskTime) {
            $total_task_time = calculatePeriodOfTimeInMinutes($taskTime->task_day_duration , $taskTime->task_time_duration );
            $total_task_interval_time = calculatePeriodOfTimeInMinutes($taskTime->interval_day_duration , $taskTime->interval_time_duration );
            $total_time = $total_time + $total_task_time + $total_task_interval_time;
        }
        $how_many_days = intdiv($total_time , enV("EACH_DAY_MINUTE_TIME")) ;
        $how_much_time = $total_time - ($how_many_days * enV("EACH_DAY_MINUTE_TIME"));
        $how_much_time = intdiv($how_much_time, 60).':'. ($how_much_time % 60) ;
        return [
            'days' => $how_many_days ,
            'time' => $how_much_time
            ] ;
        }
        return [
            'days' => '' ,
            'time' => ''
        ] ;
    }
}
