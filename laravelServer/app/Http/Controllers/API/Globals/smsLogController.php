<?php

namespace App\Http\Controllers\API\Globals;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Globals\smsLogCollection;
use App\Models\Customer;
use App\Models\smslog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class smsLogController extends Controller
{

    public function __construct() {
        $this->middleware('scopes:show-sms-logs')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $sms_logs = new smslog() ;
        $data = $sms_logs->orderBy('created_at' , 'desc')->paginate($request['per_page']);


        $data = \App\Http\Resources\API\Globals\smsLog::collection($data)->response()->getData(true);

        return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data], Response::HTTP_OK,[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
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
        $customer = null ;
        $receivers = [];
        foreach ($request['customer_number'] as $receptor ){
            if($receptor == env('SMS_ADMIN_PHONE')){
                $receivers['admin'] = $receptor;
            }
            else {
                $receivers['customer'] = $receptor;
                $customer = Customer::where('phone_number', $receptor)->first();
            if($customer)
                $customer = $customer->id ;
            }
        }
        $sms_log = new smslog() ;
        $sms_log->title = $request['title'];
        $sms_log->sms_text = $request['sms_text'] ;
        $sms_log->status = $request['status'];
        $sms_log->phone_number = json_encode($receivers);
        $sms_log->customer_id = $customer;
        $sms_log->save();

        return response()->json([
            'success' => true,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
