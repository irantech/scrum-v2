<?php

namespace App\Http\Resources\API\Task;

use App\Http\Resources\API\Task\subCollection;
use App\Http\Resources\API\ToDoList\contract;
use App\Http\Resources\API\Task\todoList;
use App\Http\Resources\API\Task\UserCollection;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleTask extends JsonResource
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
        $todo_user = User::whereIn('id' , $todo_user)->get();

        $task_id = $this->id;
        $user = new User() ;
        $userList = $user->with(['taskSubTasks' =>  function ($request) use($task_id) {
            $request->where('subtaskable_id' , $task_id);
        }])->whereHas('taskSubTasks' , function ($request) use($task_id) {
            $request->where('subtaskable_id' , $task_id);
        })->get();

        $totalTimeDuration = $this->totalTimeDuration($this->taskTimes) ;
        $last_done_todo_list = $this->todoList()->where('status' , 'done')->latest()->first();
        return [
            'id'            => $this->id ,
            'title'         => $this->title ,
            'description'   => $this->description ,
            'user_owner'    => new \App\Http\Resources\API\Task\User($this->user) ,
            'contract'       => new contract($this->contract) ,
            'status'         => $this->status ,
            'site_link'      => $this->site_link ,
            'theme_link'     => $this->theme_link ,
            'label_list'     => new TaskLabelCollection($this->taskLabels),
            'sub_task'       => new assignedCollection($userList),
            'not_assigned_sub_task'   => new subCollection($this->notAssigendsubtasks),
            'task_time'      => new TaskTimeCollection($this->taskTimes),
            'last_todo_list'      => new todoList($last_done_todo_list),
            'user_list'      => new UserCollection($todo_user),
            'total_task_day' => $totalTimeDuration['days'],
            'total_task_time'=> $totalTimeDuration['time'] == '0:0' ? 0 : $totalTimeDuration['time'],
            'delivery_time'  => $this->delivery_time ? Verta::instance($this->delivery_time)->format('Y-m-d') :'',
            'delivery_time_base' => $this->delivery_time,
            'created_at'     => Verta::instance($this->created_at)->format('Y-m-d'),
        ];
    }

    private function totalTimeDuration($taskTimes) {
        if(count($taskTimes) > 0 ) {
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
