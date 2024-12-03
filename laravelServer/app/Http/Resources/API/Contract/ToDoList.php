<?php

namespace App\Http\Resources\API\Contract;

use App\Models\taskTime;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class ToDoList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $todo_progress = $this->todoStatusTime($this);
        return [
            'id'            => $this->id ,
            'user'          => new reverseUser($this->user),
            'section'          => new reverseSection($this->user->roleSection),
            'progress_extend_time'   => $todo_progress['task_extra_last_time'],
        ];
    }

    public function todoStatusTime($todo){


        $starting_time = Carbon::parse($todo->starting_time)  ;
        $ending_time = Carbon::parse($todo->ending_time)  ;


        $diff_time = $ending_time->diff($starting_time)->format('%H:%I:%S');
        $diff_day = weekDays($starting_time , $ending_time);


        $total_diff_minute = calculatePeriodOfTimeInMinutes($diff_day  , $diff_time );
        if($todo->todoable_type == 'App\Models\ChecklistContract') {
        $checklist = \App\Models\ChecklistContract::find($todo->todoable_id)->checklist;
        }else if($todo->todoable_type == 'App\Models\Task'){
            $task = \App\Models\Task::find($todo->todoable_id);
        }

        $section = User::find($todo->user_id)->roleSection;

        $task_time = null ;

        if(isset($checklist)) {
            $task_time = \App\Models\taskTime::where('section_id' , $section->section->id)->where('tasktimeable_id' , $checklist->id)->where('task_status' , $todo->task_status)->first();
        }else if(isset($task)) {
            if($section->type != 'manager') {
                $task_time = \App\Models\taskTime::where('section_id' , $section->section->id)->where('tasktimeable_id' , $task->id)->where('task_status' , $todo->task_status)->first();
            }
        }

        if(!$task_time) {
            return  [
                'task_last_status' => '' ,
                'task_extra_last_time'   => ''
            ];
        }

        $total_task_time = calculatePeriodOfTimeInMinutes($task_time->task_day_duration , $task_time->task_time_duration );
        $total_interval_task_time = calculatePeriodOfTimeInMinutes($task_time->interval_day_duration  , $task_time->interval_time_duration );
        $total_task_duration_in_minute = $total_task_time + $total_interval_task_time ;
        if($total_diff_minute != '0' ) {

            $percent_past = ( $total_diff_minute * 100 ) / $total_task_duration_in_minute ;

            if($percent_past > 100) {
                $time_extend = $total_diff_minute - $total_task_duration_in_minute ;

                $how_many_days = intdiv($time_extend , env("EACH_DAY_MINUTE_TIME")) ;

                $remaining_minutes = $time_extend - ( $how_many_days * env("EACH_DAY_MINUTE_TIME") );
                $how_much_time = intdiv($remaining_minutes, 60).':'. ($remaining_minutes % 60) . ':00' ;
                $total_extra_time = $how_many_days . ' ' . $how_much_time;


                $result =  [
                    'task_last_status' => '100' ,
                    'task_extra_last_time'   => $total_extra_time
                ];

            }else{

                $result =     [
                    'task_last_status' => $percent_past ,
                    'task_extra_last_time'   => ''
                ];
            }
        }else{

            $result =    [
                'task_last_status' => '' ,
                'task_extra_last_time'   => ''
            ];
        }
        return  $result ;
    }
}
