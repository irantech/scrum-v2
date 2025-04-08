<?php

namespace App\Http\Resources\API\Task;

use App\Http\Resources\API\Contract\ChecklistContract;
use App\Http\Resources\API\ContractChecklist\subTaskCollection;
use App\Http\Resources\API\Task\UserCollection;
use App\Http\Resources\API\ToDoList\contract;
use App\Http\Resources\API\ToDoList\todoListCollection;
use App\Http\Resources\API\User\TodoCollection;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use phpDocumentor\Reflection\Types\Void_;

class Task extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {


        $todo_user_ids = $this->todoList()->pluck('user_id');
        $todo_users = User::whereIn('id', $todo_user_ids)->get();

        $users_with_duplicates = $todo_user_ids->map(function ($user_id) use ($todo_users) {
            return $todo_users->firstWhere('id', $user_id);
        });
//        $task_id = $this->id;
//        $user = new User() ;
//        $userList = $user->with(['taskSubTasks' =>  function ($request) use($task_id) {
//            $request->where('subtaskable_id' , $task_id);
//        }])->whereHas('taskSubTasks' , function ($request) use($task_id) {
//            $request->where('subtaskable_id' , $task_id);
//        })->get();
        $totalTimeDuration = $this->totalTimeDuration($this->taskTimes);
        if($this->status != 'running')
            $this->days_left = 0;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'user_owner' => new \App\Http\Resources\API\Task\User($this->user),
            'contract' => new contract($this->contract),
            'status' => $this->status,
            'site_link' => $this->site_link,
            'theme_link' => $this->theme_link,
            'label_list' => new TaskLabelCollection($this->taskLabels),
            'user_list' => new UserCollection($users_with_duplicates),
            'total_task_day' => $totalTimeDuration['days'],
            'total_task_time' => $totalTimeDuration['time'],
            'delivery_time' => $this->delivery_time ? Verta::instance($this->delivery_time)->format('Y-m-d') : '',
            'delivery_time_base' => $this->delivery_time,
            'created_at' => Verta::instance($this->created_at)->format('Y-m-d'),
            'days_passed_since_making_task' => $this->days_left ,
            ];


    }

    private function totalTimeDuration($taskTimes)
    {
        if (count($taskTimes) > 0) {
            $total_time = 0;
            foreach ($taskTimes as $key => $taskTime) {
                $total_task_time = calculatePeriodOfTimeInMinutes($taskTime->task_day_duration, $taskTime->task_time_duration);
                $total_task_interval_time = calculatePeriodOfTimeInMinutes($taskTime->interval_day_duration, $taskTime->interval_time_duration);
                $total_time = $total_time + $total_task_time + $total_task_interval_time;
            }

            $how_many_days = intdiv($total_time, enV("EACH_DAY_MINUTE_TIME"));
            $how_much_time = $total_time - ($how_many_days * enV("EACH_DAY_MINUTE_TIME"));
            $how_much_time = intdiv($how_much_time, 60) . ':' . ($how_much_time % 60);

            return [
                'days' => $how_many_days,
                'time' => $how_much_time
            ];
        }
        return [
            'days' => '',
            'time' => ''
        ];

    }

}
