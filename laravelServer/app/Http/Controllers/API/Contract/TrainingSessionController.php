<?php

namespace App\Http\Controllers\API\Contract;

use App\Http\Controllers\API\ToDoList\ToDoListController;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\Contract\TrainingSessionCollection;
use App\Models\ChecklistContract;
use App\Models\TrainingSession;
use App\Notifications\TrainingSessionNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\API\Contract\TrainingSession as  TrainingSessionResource;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class TrainingSessionController extends Controller
{
    public function __construct() {
        $this->middleware('scopes:show-training-session')->only(['store' , 'update']);
    }

    public function index() {
        $training_session  = new TrainingSession();
        $data = $training_session->orderBy('created_at' , 'desc')->get();
        $data = new TrainingSessionCollection($data);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'user_id' => [
                'required',
                'exists:App\Models\User,id'
            ],
            'checklist_contract_id' => [
                'required',
                'exists:App\Models\ChecklistContract,id'
            ],
            'session_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'session_time' => [
                'required',
                'date_format:H:i',
            ],
            'location_status' => [
                'required',
                'in:0,1'
            ],
            'status' =>[
                'in:set_time,done,cancel'
            ],
            'duration' =>[
                'date_time'
            ],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }
        $check_last_training_session_exist =  TrainingSession::where('checklist_contract_id' , $request['checklist_contract_id'])->orderBy('created_at' , 'desc')->first();

        $training_session = new TrainingSession();
        $training_session->user_id                  = $request['user_id'];
        $training_session->checklist_contract_id    = $request['checklist_contract_id'];
        $training_session->session_date             = $request['session_date'];
        $training_session->session_time             = $request['session_time'];
        $training_session->location_status          = $request['location_status'];
        $training_session->location_place           = $request['location_place'];
        $training_session->address                  = $request['address'];
        $training_session->status                   = 'set_time';
        $training_session->save();
        $data = new \App\Http\Resources\API\Contract\TrainingSession($training_session);

        $checklistContract = ChecklistContract::find($request['checklist_contract_id']);
        $receiver = $checklistContract->contract->customer->phone_number ;

        if($check_last_training_session_exist) {
            $notification_data = [
                'checklistContract' => $checklistContract,
                'trainingSession'   => $training_session ,
                'status'            => 'update' ,
                'old_date'          => $check_last_training_session_exist->session_date
            ];
        }else {
            $notification_data = [
                'checklistContract' => $checklistContract,
                'trainingSession'   => $training_session ,
                'status'            => 'insert'
            ];
        }




        Notification::send($receiver, new TrainingSessionNotification($notification_data));

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    public function getContractTrainingSession($checklistContract) {
        $checklistContract  = ChecklistContract::findOrfail($checklistContract);

        $training_session = new TrainingSession();
        $data = $checklistContract->trainingSessions;
        $data = new TrainingSessionCollection($data);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    public function update($trainingSession , Request $request) {

        $validator = Validator::make($request->all(), [
            'status' => [
                'required'
            ],
            'duration' =>[
                'date_format:H:i',
            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }

        $trainingSession  = TrainingSession::findOrFail($trainingSession);

        $trainingSession->status        = $request['status'];

        if($request['status'] == 'done'){

            $trainingSession->duration      = $request['duration'];
            $trainingSession->contributors  = json_encode($request['contributors']);
            $trainingSession->save();
            $checklistContract = ChecklistContract::find($trainingSession->checklist_contract_id);
            $customer = $checklistContract->contract->customer->name;

            $user = \App\Models\User::where('role_id' , 18)->first();

            $task_request = new Request();
            $task_request['task_type'] = get_class($trainingSession);
            $task_request['task_id'] = $trainingSession->id;
            $task_request['title'] =  'جلسه آموزش با شرکت '.$customer.' گذاشته شد.';
            $task_request['user_id'] = $user->id;
            $task_request['task_status'] = '1';
            $toDoList = new ToDoListController();
            $toDoList->assignTaskToUser($task_request);

        }else if($request['status'] == 'cancel'){

            $trainingSession->cancel_reason = $request['cancel_reason'];
            $trainingSession->save();

            $checklistContract = ChecklistContract::find($request['checklist_contract_id']);
            $receiver = $checklistContract->contract->customer->phone_number ;

            $notification_data = [
                'checklistContract' => $checklistContract,
                'trainingSession'   => $trainingSession ,
                'cancel_reason'     => $request['cancel_reason'] ,
                'status'            => 'cancel'
            ];

            Notification::send($receiver, new TrainingSessionNotification($notification_data));

        }



        $data = new \App\Http\Resources\API\Contract\TrainingSession($trainingSession);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );

    }

}
