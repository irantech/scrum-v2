<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Resources\API\Contract\ContractCollection;
use App\Http\Resources\API\Cusromer\Report;
use App\Http\Resources\API\Cusromer\ReportCollection;
use App\Models\Contract;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller {

    public function __construct()
    {
        $this->middleware('scopes:show-customer-contracts')->only('customer_contracts' );
        $this->middleware('scopes:show-customers')->only(['index']);
    }

    public function index() {
        $customer = new Customer();

        return response()->json( [
            'message' => __( 'scrum.api.get_success' ),
            'data'    => $customer::whereNotNull( 'old_id_customer' )->idDescending()->with( 'contracts' )->get()
        ] );
    }

    public function customer_contracts( $id ) {
        $customer = Customer::find( $id );
        $data     = Contract::where( 'old_id_customer', $customer->old_id_customer )->get();
        $data = new ContractCollection($data);

        // delete 'base_progress:contract_id,base_progress_id,percentage,status,description' from with array
        if ( $data ) {
            return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data ], Response::HTTP_OK );
        }

        return response()->json( [ 'message' => __( 'scrum.api.get_error' ), 'data' => null ], Response::HTTP_NO_CONTENT );
    }

    public function editCustomer( Request $request ) {

        return response()->json( [ 'message' => 'success', 'data' => $request->all() ] );
    }

    public function CustomerProjectStatus( $hash, $contract ) {
        $customer = new Customer();
        $cid      = $customer::with( [
            'contracts' => function ( $query ) {
                $query->with( 'base_progress' );
            }
        ])->where( 'old_whmcs_hash', $hash )->first();

        return [ 'contracts' => $cid->contracts()->with( 'base_progress' )->where( 'id', $contract )->get() ];
    }

    public function AllCustomersProjects(Request $request) {
        $customers = Customer::with([ 'contracts' => function($q) use ($request){
                $q->when($request['status'] ?? null , function ($query , $search) use ($request){
                    $query->whereHas('checklistContract.titleChecklistUserAverage' , function ($qr) use ($search , $request ) {
                        $qr->selectRaw( "AVG(status) as average" )
                            ->when($request['section'] ?? null , function ($qq , $sear) {
                                $qq->where('section_id' , $sear);
                            })->having('average' , $search == 1 ? '=' : '<' , [1.000]);
                    });
                })->when($request['contract_code'] ?? null , function ($q , $search){
                    $q->where( 'contract_code', 'like', '%' . $search . '%' );
                });
            }  ,  'contracts.checklistContract' => function($q) use ($request){
                $q->when($request['status'] ?? null , function($query , $search) use ($request) {
                    $query->whereHas('titleChecklistUserAverage' , function ($qr) use ($search , $request) {
                        $qr->selectRaw( "AVG(status) as average" )
                            ->when($request['section'] ?? null , function ($qq , $sear) {
                                $qq->where('section_id' , $sear);
                            })->having('average' , $search == 1 ? '=' : '<' , [1.000]);
                    });
                })->when($request['checklist'] ?? null , function($query , $search) {
                    $query->where('checklist_id', $search);
                });
        }])->when($request['status'] ?? null , function($query , $search) use ($request) {
                $query->whereHas('contracts.checklistContract.titleChecklistUserAverage', function($q) use ($search , $request) {
                    $q->selectRaw( "AVG(status) as average" )
                        ->when($request['section'] ?? null , function ($qq , $sear) {
                            $qq->where('section_id' , $sear);
                        })->having('average' , $search == 1 ? '=' : '<' , [1.000]);
                });
        })->when($request['checklist'] ?? null , function($query , $search){
            $query->whereHas('contracts.checklistContract', function($q) use ($search) {
                $q->where('checklist_id', $search);
            });
        })->when($request['contract_title'] ?? null , function($query , $search){
            $query->whereHas('contracts', function($q) use ($search) {
                $q->where( 'name', 'like', '%' . $search . '%' );
            });
        })->when($request['contract_code'] ?? null , function($query , $search){
            $query->whereHas('contracts', function($q) use ($search) {
                $q->where( 'contract_code', 'like', '%' . $search . '%' );
            });
        });

     $customers = $customers->orderBy('created_at','DESC')->paginate($request['per_page']);
     $data = Report::collection($customers)->response()->getData(true);

        return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data], Response::HTTP_OK,[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    }

    public function CustomersProjects($id) {
        $customer = Customer::where('id' , $id)->with([ 'contracts'  ,  'contracts.checklistContract'])->first();
        $data = new Report($customer);
        return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data ], Response::HTTP_OK,[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    }

    public function CustomerProjectsByHash( $hash ) {
        $customer = Customer::where('old_whmcs_hash' , $hash)->with([ 'contracts'
            ,  'contracts.checklistContract'=> function($q) {
                $q->where( 'checklist_id', '5');
                $q->orWhere( 'checklist_id', '8');
            }])->first();
        $cid = new Report($customer);
        if ( $cid ) {
            return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $cid ] );
        }

        return [ 'contracts' => [] ];
    }

    public function CompletePercentageProjectsByHash( $hash, $count ) {
        $cid = Customer::where('old_whmcs_hash' , $hash)->first();
        if ( $cid ) {
            $contracts = $cid->contracts()->with(['checklistContract' => function($q){
                    $q->where('checklist_id' , '5');
            }])->orderBy( 'id', 'DESC' )->take( $count )->get();

            $return    = [];
            foreach ( $contracts as $contract ) {
                $tiny_contract = new \App\Http\Resources\API\Cusromer\Contract($contract);
                $counter = 0;
                $total_percentage = 0;
                foreach ( $tiny_contract->checklistContract as $checklistContract ) {
                    $counter ++ ;
                    $percentage     = 0;
                    $count = 0 ;
                    foreach ( $checklistContract->titleChecklistUserAverage as $titleChecklistUser ) {
                        if($titleChecklistUser->section_id != 5 && $titleChecklistUser->section_id != 2 ) {
                            $count ++ ;
                            $percentage += ($titleChecklistUser->average * 100);
                        }

                    }
                    if($percentage != 0 )
                        $total_percentage += round( $percentage / $count ) ;
                }
                if($total_percentage != 0 )
                    $total_percentage = round( $total_percentage / $counter );
                if($tiny_contract->checklistContract->count() != 0 ) {
                    $return[] = [
                        'code'       => $contract->contract_code,
                        'percentage' => $total_percentage
                    ];
                }
                else {
                    $return[] = [
                        'code'       => $contract->contract_code
                    ];
                }

            }

            return response()->json( [ 'contracts' => $return ] );
        }

        return response()->json( [ 'contracts' => [] ] );
    }


    public function resourcePagination($array, $total_count, $per_page, $correct_page): Paginator
    {
        return new Paginator($array, $total_count, $per_page, $correct_page);

    }

}
