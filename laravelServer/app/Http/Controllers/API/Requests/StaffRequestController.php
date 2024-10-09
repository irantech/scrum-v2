<?php

namespace App\Http\Controllers\API\Requests;
use App\Http\Controllers\API\ToDoList\ToDoListController;
use App\Http\Resources\API\Requests\StaffRequestCollection;
use App\Models\role;
use App\Models\ToDoList;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\ToDoList\changeToDoTimeCollection;
use App\Models\StaffRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('scope:manager-show-requests')->only(['update']);

    }

    public function getManagerRequestedList(Request $request) {
        $user = Auth::user();

        $request_list = $user->managerRequests();


        if(isset($request['has_confirmed']) && $request['has_confirmed'] !== null) {

            if($request['has_confirmed'] == '-1') {
                $request['has_confirmed'] = null;
            }

            $request_list = $request_list->where('has_confirmed' ,$request['has_confirmed'] );
        }

        if(isset($request['start_time']) && !empty($request['start_time']) ) {

            $start_time = Verta::parse($request['start_time'])->datetime()->format('y-m-d');

            $request_list = $request_list->whereDate('created_at','>=' ,  $start_time);
        }
        if( isset($request['end_time']) && !empty($request['end_time']) ) {

            $end_time = Verta::parse($request['end_time'])->datetime()->format('y-m-d');
            $request_list = $request_list->whereDate('created_at','<=' ,  $end_time);

        }

        if(isset($request['section']) && !empty($request['section']) ) {

            $request_list = $request_list->whereHas('User' , function($q) use ($request ){
                $q->whereHas('role' , function($qq) use ($request ) {
                    $qq->where('section_id' , $request['section']);
                });
            });
        }

        $request_list = $request_list->get() ;
        return response()->json([
            'success' => true,
            'data'    => new StaffRequestCollection($request_list),
            'not_response_requests' => $request_list->where('has_confirmed' , null)->count(),
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    public function store(Request  $request) {
        $validator = Validator::make($request->all(), [
            'reason' => [
                'required' ,
                'JSON'
            ],
            'request_id' => [
                'required' ,
            ],
            'request_type' => [
                'required'
            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }


        // what is the task ( checklist or ticket )
        if($request['request_type'] == 'ToDoList') {
            $request_class = new ToDoList();
        }

        $request_type = $request_class->where('id' , $request['request_id'])->first();

        $user = Auth::user();
        $role = role::find($user->role_id) ;

        if($role->type == 'employee')
            $manager = $this->staffManger($user);
        else
            $manager = $user->find(1);

        $staff_request = new StaffRequest();
        $staff_request->reason = $request['reason'];
        $staff_request->manager_id = $manager->id;


        $staff_request->User()->associate($user);

        $result = $request_type->requests()->save($staff_request);

        return response()->json([
            'success' => true,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );

    }


    public function update(Request $request , $request_id){
        $validator = Validator::make($request->all() , [
            'has_confirmed' => [
                'required' ,
                'in:0,1'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }


        $staff_request = StaffRequest::find($request_id);


        $staff_request_request = [
            'has_confirmed' => $request['has_confirmed'] ,
            'manager_reason' => $request['manager_reason'] ,
        ];

        $result = $staff_request->update($staff_request_request);
        if($result) {
            $request_class = $staff_request->requestable_type;

            $class_parts = explode('\\', $request_class);
            $base_class = end($class_parts);

            if($base_class == 'ToDoList' && $staff_request_request['has_confirmed'] == '1'){
                $request_reason_data = json_decode($staff_request['reason']);

                $change_time_request = new Request();
                $change_time_request['change_time_reason'] = $request_reason_data->reason ;
                $change_time_request['starting_time'] = $request_reason_data->starting_time ;
                $change_time_request['ending_time'] = $request_reason_data->ending_time ;

                $todo = new ToDoListController() ;
                $result_status = $todo->changeTodoListTime( $change_time_request , $staff_request->requestable_id);

            }
        }


        if($result) {
            return response()->json([
                'success' => true,
                'message' => 'با موفقیت به روز رسانی شد',
            ], 200 );
        }else {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.not_found' ),
            ], 200 );
        }
    }

    public function staffManger($user){
        $role = $user->getRole();
        $section_id = $role->section->id;
        if ($section_id == 2) {
            $section_id = 3;
        }
        $role = role::where('section_id' , $section_id)->where('type' , 'manager')->first();
        return User::where('role_id' , $role->id)->first();
    }


    public function getStaffRequestedList() {
        $user = Auth::user();

        $request_list = $user->requests;

        return response()->json([
            'success' => true,
            'data'    => new StaffRequestCollection($request_list),
            'not_response_requests' => $request_list->where('has_confirmed' , null)->count(),
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }
    public function getAdminRequestedList(Request $request) {

        $request_list = new StaffRequest();

        if(isset($request['has_confirmed']) && $request['has_confirmed'] !== null) {

            if($request['has_confirmed'] == '-1') {
                $request['has_confirmed'] = null;
            }

            $request_list = $request_list->where('has_confirmed' ,$request['has_confirmed'] );
        }

        if(isset($request['start_time']) && !empty($request['start_time']) ) {

            $start_time = Verta::parse($request['start_time'])->datetime();
            $request_list = $request_list->whereDate('created_at','>=' ,  $start_time);
        }

        if(isset($request['end_time']) && !empty($request['end_time']) ){

            $end_time = Verta::parse($request['end_time'])->datetime();
            $request_list = $request_list->whereDate('created_at','<=' ,  $end_time);
        }

        if(isset($request['section']) && !empty($request['section']) ){
            $roles =  role::where('section_id' , $request['section'])->pluck('id');
//            $request_list = $request_list->with(['User' => function($q) use ($roles) {
//                $q->whereIn('role_id' ,$roles);
//            }]);

        }
        $request_list = $request_list->get() ;

        return response()->json([
            'success' => true,
            'data'    => new StaffRequestCollection($request_list),
            'not_response_requests' => $request_list->where('has_confirmed' , null)->count(),
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }
}
