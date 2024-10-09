<?php

namespace App\Http\Resources\API\ToDoList;

use App\Http\Controllers\API\ToDoList\ToDoListController;
use App\Http\Resources\API\Globals\holidayCollection;
use App\Models\holiday;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\ToDoList\ChecklistContract;

class todoList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $task_class = $this->todoable_type;

        $task_model =    new $task_class ;
       
        $task = $task_model->where('id' , $this->todoable_id)->first();

        $class_parts = explode('\\', $task_class);
        $base_class_model = end($class_parts);
        $base_class = 'App\Http\Resources\API\ToDoList\\'.$base_class_model;

        $todo_progress = $this->todoStatusTime($this);



//        if($this->status == 'stop') {
//            $customer_hold = $task->customerHold()->orderBy('id' , 'desc')->first();
//
//            if($customer_hold->ending_time == null) {
//
//               $diffMinute = verta($customer_hold->starting_time)->diffMinutes();
//
//               $customer_hold_day_stop = $diffMinute/60/24 ;
//               $customer_hold_hour_stop = $diffMinute/60 ;
//
//               $customer_hold_total_stop = intval($customer_hold_day_stop) . ' ' . intval($customer_hold_hour_stop);
//            }
//
//        }

        return [
            'id'            => $this->id ,
            'title'         => $this->title ,
            'user'          => new \App\Http\Resources\API\User\User($this->user) ,
            'description'   => $this->description ,
            'status'        => $this->status ,
            'starting_time' => $this->starting_time ? Verta::instance($this->starting_time)->format((' j %B Y ')) :'',
            'starting_time_base' => $this->starting_time ? $this->starting_time :'',
            'ending_time'   => $this->ending_time ? Verta::instance($this->ending_time)->format((' j %B Y ')) : '',
            'ending_time_base'   => $this->ending_time ? $this->ending_time : '',
            'created_at'    => Verta::instance($this->created_at)->format(('Y-m-d')),
            'created_time'  => Verta::instance($this->created_at)->format((' j %B Y ')),
            'type'          => end($class_parts),
            'task_class'    => $task_class ,
            'task_id'       => $task->id,
            'task'          => new $base_class($task),
            'change_time_reason' => $this->change_time_reason,
            'todo_status'   => $this->todo_status ,
            'difference_time'        => $this->difference_time ,
            'progress_percent'       => $todo_progress['task_last_status'],
            'progress_extend_time'   => $todo_progress['task_extra_last_time'],
            'requests'               => $this->requests,
//            'customer_hold_time'     => $this->status == 'stop' && isset($customer_hold_total_stop)  ? $customer_hold_total_stop : ''
        ];
    }

    public function todoStatusTime($todo){


        if($todo->status == 'done' || $todo->status == 'started') {
            return  [
                'task_last_status' => '' ,
                'task_extra_last_time'   => ''
            ];
        }

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

        if($total_diff_minute != 0 ) {
            $percent_past = ( $total_diff_minute * 100 ) / $total_task_duration_in_minute ;

            if($percent_past > 100) {

                $time_extend = $total_diff_minute - $total_task_duration_in_minute ;

                $how_many_days = intdiv($time_extend , enV("EACH_DAY_MINUTE_TIME")) ;

                $remaining_minutes = $time_extend - ( $how_many_days * enV("EACH_DAY_MINUTE_TIME") );
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
