<?php

namespace App\Http\Controllers\API\ToDoList;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\ToDoList\TaskCollection;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    public function __construct()
    {
        $this->middleware('scope:manage-tasks')->only(['store' , 'update']);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $task = new Task();

        $task_list =  $task->all();

        return response()->json([ 'message' => __( 'scrum.api.insert_success') , 'data' => new TaskCollection($task_list)]);

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

        $task = new task();
        $task->section_id = $request['section_id'] ;
        $task->checklist_id = $request['checklist_id'] ;
        $task->task_time_duration = $request['task_time_duration'] ;
        $task->task_day_duration = $request['task_day_duration'] ;
        $task->interval_time_duration = $request['interval_time_duration'] ;
        $task->interval_day_duration = $request['interval_day_duration'] ;
        $task->task_status = $request['task_status'] ;
        $task->description = $request['description'] ;
        $task->save();

        return response()->json([
            'success' => true,
            'data'   => new \App\Http\Resources\API\ToDoList\Task($task)  ,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return response()->json(new \App\Http\Resources\API\ToDoList\Task($task),
            Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
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
        $task->section_id = $request->section_id ;
        $task->checklist_id = $request->checklist_id ;
        $task->task_time_duration  = $request->task_time_duration;
        $task->task_day_duration  = $request->task_day_duration;
        $task->interval_time_duration  = $request->interval_time_duration;
        $task->interval_day_duration  = $request->interval_day_duration;
        $task->task_status  = $request->task_status;
        $task->description  = $request->description;
        $task->save();

        return response()->json([
            'success' => true,
            'data'    => new  \App\Http\Resources\API\ToDoList\Task($task),
            'message' => __( 'scrum.api.update_success' ),
        ], 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
}
