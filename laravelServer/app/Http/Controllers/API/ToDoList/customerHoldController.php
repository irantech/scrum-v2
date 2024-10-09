<?php

namespace App\Http\Controllers\API\ToDoList;

use App\Http\Controllers\Controller;
use App\Models\ChecklistContract;
use App\Models\CustomerHold;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class customerHoldController extends Controller
{
    public function __construct(){
    }

    public function sendProjectToCustomer(Request $request){

        $validator = Validator::make($request->all(), [
            'contract_checklist_id' => [
                'required',
                'exists:App\Models\ChecklistContract,id'
            ],
            'section_id' => [
                'required',
                'exists:App\Models\Section,id'
            ],

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }
        $customer_hold = new CustomerHold();
        $customer_hold->section_id = $request['section_id'];
        $customer_hold->checklist_contract_id = $request['contract_checklist_id'];
        $customer_hold->starting_time = Carbon::now();
        $customer_hold->user_id = Auth::user()->id;
        $customer_hold = $customer_hold->save();

        $todo_list = new ToDoListController() ;
        $todo_list = $todo_list->stopReturnLastTodoList($request['contract_checklist_id'] , 'stop');
        if($todo_list && $customer_hold)  {
            return response()->json([
                'success' => true,
                'message' => __( 'scrum.api.insert_success' ),
            ], 200 );
        }else {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_success' ),
            ], 403 );

        }

    }

    public function returnProjectFromCustomer($checklist_contract_id){
        $customer_hold = CustomerHold::where('checklist_contract_id' , $checklist_contract_id)->orderBy('id','desc')->first();

        $customer_hold = $customer_hold->update([
            'ending_time' => Carbon::now()
        ]);
        $todo_list = new ToDoListController();
        $todo_list = $todo_list->stopReturnLastTodoList($checklist_contract_id , 'return');
        if($todo_list && $customer_hold)  {
            return response()->json([
                'success' => true,
                'message' => __( 'scrum.api.insert_success' ),
            ], 200 );
        }else {
            return response()->json([
                'success' => false,
                'message' => 'error has occurred',
            ], 403 );

        }
    }

    public function getCustomerHoldByChecklistContract($checklist_contract_id) {
        $checklist_contract = ChecklistContract::find($checklist_contract_id);
        $customer_hold = $checklist_contract->customerHold()->orderBy('id' , 'desc')->first();
        return response()->json([
            'success' => true,
            'data'    => $customer_hold,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }
}
