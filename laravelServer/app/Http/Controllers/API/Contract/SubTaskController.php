<?php

namespace App\Http\Controllers\API\Contract;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Contract\ChecklistContract;
use App\Http\Resources\API\Contract\checklistProcessCollection;
use App\Http\Resources\API\Contract\subTaskCollection;
use App\Http\Resources\API\Task\detailCollection;
use App\Models\Checklist;
use App\Models\ChecklistProcess;
use App\Models\SubTask;
use App\Models\Section;
use App\Models\Task;
use App\Models\ToDoList;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SubTaskController extends Controller
{
    public function __construct() {
//        $this->middleware('scopes:reply-sub-task')->only('reply');
    }


    public function create(Request $request){
        $request->validate( [
            'status'    => 'required|string',
            'body'      => 'required|string'
        ]);
//        if(!$request['section']) {
//            $section = $checklist_process->section_id ;
//        }else {
//            $section = $request['section'];
//        }
        if(isset($request['checklist_process']) && !empty($request['checklist_process'])){
            $task = ChecklistProcess::find($request['checklist_process']);
            $last_sub_task = $task->subtasks()->whereNull('parent_id' )->orderBy('id' , 'desc')->first();
        }
        else if(isset($request['task']) && !empty($request['task'])){
            $task = Task::find($request['task']);
            $last_sub_task = $task->subtasks()->whereNull('parent_id' )->orderBy('id' , 'desc')->first();
        }


        $x=$task->subtasks()->create([
            'status' => $request->status,
            'description' => $request->body,
            'order' => $last_sub_task ? $last_sub_task->order + 1  : '1',
            'user_id' => Auth::user()->id,
        ]);
        dd($x);


        $sub_task = $task->subtasks()->orderBy('created_at' , 'desc')->first();

        $sub_task->key_id = $sub_task->id ;
        $sub_task->save();

        return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new \App\Http\Resources\API\ContractChecklist\subTask($sub_task) ], 200 );

    }


    public function updateFile(SubTask $subTask , Request $request) {
        $request->validate( [
            'file'    => 'required|string',
        ]);
        if($subTask->file_list)
        {
            $object = json_decode($subTask->file_list , true);
            array_push($object,  $request->file);
            $json = json_encode($object);
        }
        else {
            $arr = array('1' => $request->file);
            $json = json_encode($arr);
        }
        $subTask->file_list = $json;
        $subTask->save();
        return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new \App\Http\Resources\API\ContractChecklist\subTask($subTask) ], 200 );
    }

    public function reply(Request $request) {

        $request->validate( [
            'status'    => 'required|string',
            'body'      => 'required|string',
            'parent_id' => 'required'
        ]);
        $has_access = false;
        $sub_task_parent = SubTask::find($request['parent_id']);
        if(isset($request['checklist_process'])){
            $task = ChecklistProcess::find($request['checklist_process']);
            $checklistContract = \App\Models\ChecklistContract::findOrFail($task->checklist_contract_id);
            $titleUser = $checklistContract->titleChecklistUser()->where(function($query) use ($task , $sub_task_parent){
                $query->where('section_id', $task->section_id)->orWhere('section_id' , $sub_task_parent->section_id);
            })->groupBy('section_id')->get();

            $userList = [] ;

            foreach ($titleUser as $key => $user) {
                $userList[$key] = $user->user_id ;
            }
            $has_access = $this->canReply($userList , $task->user_id , $task->section_id);
        }

        else if(isset($request['task'])){
            $task = Task::find($request['task']);
            $has_access = true ;
        }

        if ($has_access) {
            $sub_task = new SubTask() ;
            $sub_task->status = $request['status'];
            $sub_task->description = $request['body'];
            $sub_task->parent_id = $request['parent_id'];
            $sub_task->subtaskable_id = $task->id;
            $sub_task->subtaskable_type = get_class($task);
            $sub_task->key_id = $sub_task_parent->key_id ;
            $sub_task->user_id = Auth::user()->id ;
            $sub_task->save();

            return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new \App\Http\Resources\API\ContractChecklist\subTask($sub_task) ], 200 );
        }
        return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
    }


    public function editReply(SubTask $sub_task , Request $request) {
        $request->validate( [
            'status'    => 'required|string',
            'body'      => 'required|string'
        ]);

//        echo json_encode($sub_task->user_id);
//        die();
//        if ($this->canReply($sub_task->user_id)) {
            $sub_task->status = $request['status'];
            $sub_task->description = $request['body'];
            $sub_task->save();
            return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new \App\Http\Resources\API\ContractChecklist\subTask($sub_task) ], 200 );
//        }
//        return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
    }

    public function seenSubTask(SubTask $sub_task) {

        if($sub_task->seen == 1) {
            $sub_task->seen = 0 ;
        }else{
            $sub_task->seen = 1 ;
        }
        $sub_task->save();
        return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') ], 200 );
    }

    public function deleteImage(SubTask $sub_task , Request $request) {
        $request->validate( [
            'file'    => 'required|string'
        ]);
//        if ($this->canReply($sub_task->user_id)) {
            $jsonArray = json_decode($sub_task->file_list , true);
            foreach ($jsonArray as $key => $attachment ){
               if($attachment == $request->file)
                   unset($jsonArray[$key]);
            }
            $newJson = json_encode($jsonArray);
            $sub_task->file_list = $newJson;
            $sub_task->save();
            return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new \App\Http\Resources\API\ContractChecklist\subTask($sub_task) ], 200 );
//        }
        return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
    }

    public function canReply($user , $user_creator = null , $section = null) : bool{
        $user_creator_section = User::find($user_creator)->roleSection->section;
        if(is_array($user)){
            foreach ($user as $use) {
                if(Auth::user()->id == $use){
                    return true;
                }
            }
        }else {
            if(Auth::user()->id == $user) {
                return true;
            }
        }
        if(Auth::user()->getRole()->title == 'admin' || Auth::user()->id == $user_creator ) {
            return true;
        }

        $data = false ;
        if( $user_creator_section != null  )
        {
            $data = $this->canManager($user_creator_section->id);
        }
        if( !$data  && $section != null )
        {
            $data = $this->canManager($section);
        }

        return $data;
    }
    public function orderSubTask() {
        $checklistProcess =  ChecklistProcess::withCount('subtasks')->where('status' , 0)->get();
       foreach ($checklistProcess as $process) {

           if($process->subtasks_count > 0 ) {

               $subTasks =  SubTask::where('checklist_process_id' , $process->id)->whereNull('parent_id')->get();
               $counter = 1 ;
               foreach ($subTasks as $subTask) {
                   $subTask->order = $counter;
                   $subTask->save();
                   $counter++ ;
               }
           }

       }
        die();
    }


    public function addKeySubTask() {
        $subTasks = SubTask::whereNull('parent_id')->get();
        foreach ($subTasks as $subTask) {
            $subTask->key_id = $subTask->id;
            $subTask->save();
            $this->setAllRepliesKey($subTask->allReplies , $subTask->id);
        }
        die();
    }

    private function setAllRepliesKey($replies , $key_id) {


        if(empty($replies)){
            return null;
        }
        foreach ($replies as $reply){
            $reply->key_id = $key_id;
            $reply->save();
            if(!empty($reply->allReplies)){

                $this->setAllRepliesKey($reply->allReplies , $key_id);
            }
        }
    }

    public function canManager( $section ){
        switch ($section) {
            case(1):
                return (Auth::user()->getRole()->title == 'administrative manager');
            case(2):
            case(3):
            case(7):
                return (Auth::user()->getRole()->title == 'technical Manager');
            case(4):
                return (Auth::user()->getRole()->title == 'support Manager');
            case(5):
                return (Auth::user()->getRole()->title == 'sales manager');
            default:
                return  false ;
        }
    }

    public function subTasks(Request $request) {
        $data = [] ;
        $sub_tasks = [] ;
        if($request['type'] == ''  || (isset($request['type']) && $request['type'] == 'checklist')) {
            $checkListContract  = new \App\Models\ChecklistContract() ;
            $subTaskList = $checkListContract
                ->with('doneTodoList')
                ->withCount('subTaskProcess')
                ->whereHas('subTaskProcess', function($query) use ($request) {
                    if(isset($request['start_date']) && !empty($request['start_date'])  ){
                        $start_time = Verta::parse($request['start_date'])->datetime()->format('y-m-d');
                        $query->whereDate('created_at', '>=' , $start_time);
                    }
                    if(isset($request['end_date']) && !empty($request['end_date'])) {
                        $end_time = Verta::parse($request['end_date'])->datetime()->format('y-m-d');
                        $query->whereDate('created_at', '<=' , $end_time);
                    }
                    if(isset($request['section']) && !empty($request['section'])) {
                        $query->where( 'checklist_processes.section_id',  $request['section'] );
                    }
                })
                ->whereHas('subTaskProcess', function($query) use ($request) {
                    $query->when($request['user'] ?? null , function($q , $search){
                        $q->join('title_checklist_user', 'checklist_processes.checklist_contract_id', '=', 'title_checklist_user.checklist_contract_id')->where('title_checklist_user.user_id' , $search)->groupBy('title_checklist_user.checklist_contract_id');                    });
                })
                ->with(['subTaskProcess' => function($query) use ($request) {
                    $query->when($request['section'] ?? null , function($q , $request){
                        $q->where( 'checklist_processes.section_id',  $request);
                    });
                    $query->when($request['user'] ?? null , function($q , $search){
                        $q->join('title_checklist_user', 'checklist_processes.checklist_contract_id', '=', 'title_checklist_user.checklist_contract_id')->where('title_checklist_user.user_id' , $search)->groupBy('title_checklist_user.checklist_contract_id');
                    });
                }, 'subTaskProcess.subtasks' => function($query_sub_task) use ($request){
                    $query_sub_task->withCount('replies');
                }])
                ->when($request['contract_title'] ?? null , function($query , $search){
                    $query->whereHas('contract.customer', function($q) use ($search) {
                        $q->where( 'name', 'like', '%' . $search . '%' );
                    });
                })
                ->having('sub_task_process_count' , '>' , 0)
                ->orderBy('created_at' , 'desc')->get();
            $data = new subTaskCollection($subTaskList);
        }
//        if($request['type'] == '' || (isset($request['type']) && $request['type'] == 'task')) {
//
//            $task_model = new Task();
//            $task_list = $task_model
//                ->with('doneTodoList')
//                ->withCount('subtasks')
//                ->with(['subtasks' =>  function($query) use ($request) {
//                    if(isset($request['user']) && !empty($request['user'])) {
//                        $query->where('assigned' , $request['user']);
//                    }
//                    $query->when($request['section'] ?? null , function($query_sec , $search){
//                        $query_sec->whereHas('assignedSection.roleSection.section', function($q) use ($search) {
//                            $q->where( 'section_id',$search);
//                        });
//                    });
//                }])
//                ->whereHas('subtasks', function($query) use ($request) {
//                    if(isset($request['start_date']) && !empty($request['start_date'])  ){
//                        $start_time = Verta::parse($request['start_date'])->datetime()->format('y-m-d');
//                        $query->whereDate('created_at', '>=' , $start_time);
//                    }
//                    if(isset($request['end_date']) && !empty($request['end_date'])) {
//                        $end_time = Verta::parse($request['end_date'])->datetime()->format('y-m-d');
//                        $query->whereDate('created_at', '<=' , $end_time);
//                    }
//                    if(isset($request['user']) && !empty($request['user'])) {
//                        $query->where('assigned' , $request['user']);
//                    }
//                    $query->when($request['section'] ?? null , function($query_sec , $search){
//                        $query_sec->whereHas('assignedSection.roleSection.section', function($q) use ($search) {
//                            $q->where( 'section_id',$search);
//                        });
//                    });
//                })
//
//                ->when($request['contract_title'] ?? null , function($query , $search){
//                    $query->whereHas('contract.customer', function($q) use ($search) {
//                        $q->where( 'name', 'like', '%' . $search . '%' );
//                    });
//                })
//                ->having('subtasks_count' , '>' , 0)
//                ->orderBy('created_at' , 'desc')->get();
//            $sub_tasks = new \App\Http\Resources\API\Task\SubTaskCollection($task_list);
//        }
//        return response()->json(['message' => __('scrum.api.get_success') , 'data' => $data , 'tasks' => $sub_tasks], Response::HTTP_OK);

        return response()->json(['message' => __('scrum.api.get_success') , 'data' => $data ], Response::HTTP_OK);

    }

    public function assignSubTaskToUser(Request $request) {

        if(isset($request->task)) {

            $task = Task::find($request->task) ;

        }

        $subTasks = $task->subtasks()->whereIn('id' , $request->subTaskList)->get();

        foreach ($subTasks as $subTask) {
            $subTask->assigned  = $request->user ;
            $subTask->save();
        }

        return true;

    }

}
