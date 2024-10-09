<?php

namespace App\Http\Controllers\API\ToDoList;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\ToDoList\taskTimeCollection;
use App\Models\Checklist;
use App\Models\Task;
use App\Models\taskTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TaskTimeController extends Controller
{

    public function __construct()
    {
//        $this->middleware('scope:manage-task-times')->only(['store' , 'update']);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $task_time = new taskTime();

        $task_list =  $task_time->all();

        return response()->json([ 'message' => __( 'scrum.api.insert_success'  , [ 'item' => trans_choice( 'scrum.title.task_time', 1 ) ]) , 'data' => new taskTimeCollection($task_list)]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => [
                'required',
                'exists:App\Models\Section,id'
            ],

            'task_time_duration' => [
                'date_format:H:i'
            ],
            'task_day_duration' => [
                'integer'
            ],
            'interval_time_duration' => [
                'date_format:H:i'
            ],
            'interval_day_duration' => [
                'integer'
            ],
            'task_status' => [
                'required' ,
                'in:1,0'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }

        if(isset($request['checklist_id'])){
            $task = Checklist::find($request['checklist_id']);
        }elseif ($request['task']) {
            $task = Task::find($request['task']);
            $task_duplicated = $task->TaskTimes()->where('section_id' , $request->section_id)->first();
            if($task_duplicated) {
                return response()->json(['message' => 'شما قبلا تایم این بخش را داده اید'], Response::HTTP_FORBIDDEN);;
            }
        }

        $task->TaskTimes()->create([
            'section_id' => $request->section_id,
            'task_time_duration' => $request->task_time_duration,
            'task_day_duration' => $request->task_day_duration,
            'interval_time_duration' => $request->interval_time_duration,
            'interval_day_duration' => $request->interval_day_duration,
            'task_status' => $request->task_status,
            'description' => $request->description
        ]);

        $task_time = $task->TaskTimes()->orderBy('created_at' , 'desc')->first();


        return response()->json([
            'success' => true,
            'data'   => new \App\Http\Resources\API\ToDoList\taskTime($task_time)  ,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\taskTime  $task_time
     * @return \Illuminate\Http\Response
     */
    public function show(taskTime $task_time)
    {
        return response()->json(new \App\Http\Resources\API\ToDoList\taskTime($task_time),
            Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\taskTime  $task_time
     * @return \Illuminate\Http\Response
     */
    public function edit(taskTime $task_time)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\taskTime  $task_time
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, taskTime $task_time)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => [
                'required',
                'exists:App\Models\Section,id'
            ],
            'checklist_id' => [
                'required',
                'exists:App\Models\Checklist,id'
            ],
            'task_time_duration' => [
                'date_format:H:i'
            ],
            'task_day_duration' => [
                'integer'
            ],
            'interval_time_duration' => [
                'date_format:H:i'
            ],
            'interval_day_duration' => [
                'integer'
            ],
            'task_status' => [
                'required' ,
                'in:1,0'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }
        if(isset($request['checklist_id'])){
            $task = Checklist::find($request['checklist_id']);
        }
        $task_time->section_id = $request->section_id ;
        $task_time->tasktimeable_type = get_class($task) ;
        $task_time->tasktimeable_id = $task->id ;
        $task_time->task_time_duration  = $request->task_time_duration;
        $task_time->task_day_duration  = $request->task_day_duration;
        $task_time->interval_time_duration  = $request->interval_time_duration;
        $task_time->interval_day_duration  = $request->interval_day_duration;
        $task_time->task_status  = $request->task_status;
        $task_time->description  = $request->description;
        $task_time->save();

        return response()->json([
            'success' => true,
            'data'    => new  \App\Http\Resources\API\ToDoList\taskTime($task_time),
            'message' => __( 'scrum.api.update_success' , [ 'item' => trans_choice( 'scrum.title.task_time', 1 ) ] ),
        ], 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\taskTiem  $taskTime
     * @return \Illuminate\Http\Response
     */
    public function destroy(taskTime $taskTime)
    {
        //
    }
}
