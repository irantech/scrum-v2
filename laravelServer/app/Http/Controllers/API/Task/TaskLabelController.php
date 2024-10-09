<?php

namespace App\Http\Controllers\API\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Task\TaskLabelCollection;
use App\Http\Resources\API\ToDoList\taskTimeCollection;
use App\Models\TaskLabel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskLabelController extends Controller
{
    protected $table;

    public function  __construct(){
        $taskLabel     = new TaskLabel();
        $this->table   = $taskLabel->getTable();

        $this->middleware('scopes:create-taskLabel')->only('store');
        $this->middleware('scopes:update-taskLabel')->only('update');
        $this->middleware('scopes:delete-taskLabel')->only('destroy');
        $this->middleware('scopes:restore-taskLabel')->only('restore');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $task_labels = TaskLabel::all();
        $data = new TaskLabelCollection($task_labels);
        return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data ] );
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
        $task_label = new TaskLabel();

        $request->validate([
            'title'       => "required|max:50|unique:$this->table",
            'color'       => "required|string|max:50|",
        ]);

        $task_label->title       = $request->title;
        $task_label->color       = $request->color;
        $task_label->save();

        $task_label = new \App\Http\Resources\API\Task\TaskLabel($task_label);
        return response()->json(['message' => __('scrum.api.insert_success'  , [ 'item' => trans_choice( 'scrum.title.task_label', 1 ) ]), 'data' => $task_label], Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task_model = new TaskLabel() ;
        $task_label = $task_model->find($id) ;
       
        $task_label->title = $request->title;
        $task_label->color = $request->color ;
        $task_label->save();
        $task_label = new \App\Http\Resources\API\Task\TaskLabel($task_label);

        return response()->json( [
            'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.task_label', 1 ) ] ),
            'data'    => $task_label
        ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task_model = new TaskLabel() ;
        $task_label = $task_model->find($id) ;
        $task_label->delete();
        return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.task_label', 1 ) ] ) ] );
    }

    public function restore($id){
        $task_label = TaskLabel::withTrashed()->findOrFail($id);
        $task_label->restore();
        return response()->json( [ 'message' => __( 'scrum.api.restore_success', [ 'item' => trans_choice( 'scrum.title.task_label', 1 ) ] ) ], Response::HTTP_OK );
    }

}
