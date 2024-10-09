<?php

namespace App\Http\Controllers\API\Contract;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Contract\ChecklistContract;
use App\Http\Resources\API\Contract\checklistProcessCollection;
use App\Http\Resources\API\Contract\checklistReverseCollection;
use App\Http\Resources\API\ContractChecklist\reverse;
use App\Models\ChecklistProcess;
use App\Models\ChecklistReverse;
use App\Models\Section;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ChecklistReverseController extends Controller
{
    public function __construct() {
        $this->middleware('scopes:reply-reversed-checklist')->only('reply');
    }

    public function create(ChecklistProcess $checklist_process , Request $request){
        $request->validate( [
            'status'    => 'required|string',
            'body'      => 'required|string'
        ]);
//        if(!$request['section']) {
//            $section = $checklist_process->section_id ;
//        }else {
//            $section = $request['section'];
//        }
        $checklist_reverse = new ChecklistReverse() ;
        $last_checklist_reverse = $checklist_reverse->where('checklist_process_id',$checklist_process->id )->whereNull('parent_id' )->orderBy('id' , 'desc')->first();
        $checklist_reverse->status = $request['status'];
        $checklist_reverse->description = $request['body'];
        $checklist_reverse->checklist_process_id = $checklist_process->id;
        $checklist_reverse->order = $last_checklist_reverse ? $last_checklist_reverse->order + 1  : '1';
        $checklist_reverse->user_id = Auth::user()->id ;
        $checklist_reverse->save();
        $checklist_reverse->key_id = $checklist_reverse->id ;
        $checklist_reverse->save();
        return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new reverse($checklist_reverse) ], 200 );

    }

    public function updateFile(ChecklistReverse $checklistReverse , Request $request) {
        $request->validate( [
            'file'    => 'required|string',
        ]);
        if($checklistReverse->file_list)
        {
            $object = json_decode($checklistReverse->file_list , true);
            array_push($object,  $request->file);
            $json = json_encode($object);
        }
        else {
            $arr = array('1' => $request->file);
            $json = json_encode($arr);
        }
        $checklistReverse->file_list = $json;
        $checklistReverse->save();
        return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new reverse($checklistReverse) ], 200 );
    }

    public function reply(ChecklistProcess $checklist_process , Request $request) {
        $request->validate( [
            'status'    => 'required|string',
            'body'      => 'required|string',
            'parent_id' => 'required'
        ]);
        $checklistContract = \App\Models\ChecklistContract::findOrFail($checklist_process->checklist_contract_id);
        $checklist_reverse_parent = ChecklistReverse::find($request['parent_id']);
        $titleUser = $checklistContract->titleChecklistUser()->where(function($query) use ($checklist_process , $checklist_reverse_parent){
            $query->where('section_id', $checklist_process->section_id)->orWhere('section_id' , $checklist_reverse_parent->section_id);
        })->groupBy('section_id')->get();
        $userList = [] ;
        foreach ($titleUser as $key => $user) {
            $userList[$key] = $user->user_id ;
        }
        if ($this->canReply($userList , $checklist_process->user_id , $checklist_process->section_id)) {
            $checklist_reverse = new ChecklistReverse() ;
            $checklist_reverse->status = $request['status'];
            $checklist_reverse->description = $request['body'];
            $checklist_reverse->parent_id = $request['parent_id'];
            $checklist_reverse->checklist_process_id = $checklist_process->id;
            $checklist_reverse->key_id = $checklist_reverse_parent->key_id ;
            $checklist_reverse->user_id = Auth::user()->id ;
            $checklist_reverse->save();

            return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new reverse($checklist_reverse) ], 200 );
        }
        return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
    }


    public function editReply(ChecklistReverse $checklist_reverse , Request $request) {
        $request->validate( [
            'status'    => 'required|string',
            'body'      => 'required|string'
        ]);

        if ($this->canReply($checklist_reverse->user_id)) {
            $checklist_reverse->status = $request['status'];
            $checklist_reverse->description = $request['body'];
            $checklist_reverse->save();
            return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new reverse($checklist_reverse) ], 200 );
        }
        return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
    }
    public function seenChecklistReverse(ChecklistReverse $checklist_reverse) {
        if($checklist_reverse->seen == 1) {
            $checklist_reverse->seen = 0 ;
        }else{
            $checklist_reverse->seen = 1 ;
        }
        $checklist_reverse->save();
        return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') ], 200 );
    }

    public function deleteImage(ChecklistReverse $checklist_reverse , Request $request) {
        $request->validate( [
            'file'    => 'required|string'
        ]);
        if ($this->canReply($checklist_reverse->user_id)) {
            $jsonArray = json_decode($checklist_reverse->file_list , true);
            foreach ($jsonArray as $key => $attachment ){
               if($attachment == $request->file)
                   unset($jsonArray[$key]);
            }
            $newJson = json_encode($jsonArray);
            $checklist_reverse->file_list = $newJson;
            $checklist_reverse->save();
            return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success') , 'data' => new reverse($checklist_reverse) ], 200 );
        }
        return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
    }

    public function canReply($user , $user_creator = null , $section = null) : bool{
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
        else if($section != null)
        {
            switch ($section) {
                case(1):
                    return (Auth::user()->getRole()->title == 'administrative manager');
                case(2):
                case(3):
                case(4):
                    return (Auth::user()->getRole()->title == 'technical Manager');
                case(5):
                    return (Auth::user()->getRole()->title == 'support Manager');
                case(6):
                    return (Auth::user()->getRole()->title == 'sales manager');
                default:
                    return false;
            }
        }
        else
            return false;
    }
    public function orderReverse() {
        $checklistProcess =  ChecklistProcess::withCount('checklistReverses')->where('status' , 0)->get();


       foreach ($checklistProcess as $process) {

           if($process->checklist_reverses_count > 0 ) {

               $checklistReverse =  ChecklistReverse::where('checklist_process_id' , $process->id)->whereNull('parent_id')->get();
               $counter = 1 ;
               foreach ($checklistReverse as $reverse) {
                   $reverse->order = $counter;
                   $reverse->save();
                   $counter++ ;
               }
           }

       }
        die();
    }
       

    public function addKeyReverse() {
        $checklistReverse = ChecklistReverse::whereNull('parent_id')->get();
        foreach ($checklistReverse as $reverse) {
            $reverse->key_id = $reverse->id;
            $reverse->save();
            $this->setAllRepliesKey($reverse->allReplies , $reverse->id);
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
    public function checklistReverse(Request $request) {
        $checkListContract  = new \App\Models\ChecklistContract() ;
        $ReverseProcess = $checkListContract
            ->with('doneTodoList')
            ->withCount('reversedChecklistProcess')
            ->whereHas('reversedChecklistProcess', function($query) use ($request) {
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
            })->whereHas('reversedChecklistProcess', function($query) use ($request) {
                $query->when($request['user'] ?? null , function($q , $search){
                    $q->join('title_checklist_user', 'checklist_processes.section_id', '=', 'title_checklist_user.section_id')->where('title_checklist_user.user_id' , $search);
                });
            })->with(['reversedChecklistProcess' => function($query) use ($request) {
                $query->when($request['section'] ?? null , function($q , $request){
                    $q->where( 'checklist_processes.section_id',  $request);
                });
                $query->when($request['user'] ?? null , function($q , $search){
                    $q->join('title_checklist_user', 'checklist_processes.section_id', '=', 'title_checklist_user.section_id')->where('title_checklist_user.user_id' , $search)->groupBy('title_checklist_user.user_id');
                });
            }, 'reversedChecklistProcess.checklistReverses' => function($query_reverse) use ($request){
                $query_reverse->withCount('replies');
            }])
            ->when($request['contract_title'] ?? null , function($query , $search){
                $query->whereHas('contract.customer', function($q) use ($search) {
                    $q->where( 'name', 'like', '%' . $search . '%' );
                });
            })
            ->having('reversed_checklist_process_count' , '>' , 0)
            ->orderBy('created_at' , 'desc')->get();


        $data = new checklistReverseCollection($ReverseProcess);
        return response()->json(['message' => __('scrum.api.get_success') , 'data' => $data], Response::HTTP_OK);

    }

}
