<?php

namespace App\Http\Controllers\API\Contract;

use App\Http\Resources\API\Contract\ContractCollection;
use App\Models\Contract;
use App\Models\ContractProgress;
use App\Models\ContractSubProgress;
use App\Http\Controllers\Controller;
use App\Models\SubProgress;
use DateTime;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractController extends Controller
{

    public function __construct() {
        $this->middleware('scopes:create-contract')->only('store');
        $this->middleware('scopes:update-contract')->only('update');
        $this->middleware('scopes:delete-contract')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contract = new Contract();
        $data = $contract::orderBy('start_date', 'DESC')
            ->whereNotNull('old_id_customer')
            ->get();
        $data = new ContractCollection($data);
//        foreach ($data as $item) {
//            foreach ($item->checklistContract as $checklist_contract){
//                $item->checklists = $checklist_contract->checklistProcess()->orderBy('created_at' , 'DESC')->first();
//            }
//            $item->jalali_created_at = Verta::instance($item->created_at)->formatDatetime();
//            $item->jalali_sign_date = Verta::instance($item->sign_date)->formatDate();
//            $item->jalali_start_date = Verta::instance($item->start_date)->formatDate();
//            $item->jalali_end_date = Verta::instance($item->end_date)->formatDate();
//        }
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
    }

    public function user_contract_list()
    {
        $contract = new Contract();
        $user = auth()->user();
        $data = $contract::idDescending()->where('user_id', $user->id)->with(['customer:id,name,email', 'base_progress:contract_id,base_progress_id,percentage,status,description'])->get(['id', 'user_id', 'title', 'description', 'contract_code', 'start_date', 'end_date', 'sign_date']);

        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'customer' => 'required|exists:App\Models\User,id',
            'type' => 'required|exists:App\Models\ContractType,id',
            'title' => 'required',
            'sign_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'string|required',
            'code' => 'alpha_dash|unique:contracts,contract_code',
            'progress' => 'array',
            'progress.*' => 'exists:App\Models\BaseProgress,id'
        ]);
        $contract = new Contract();
        $contract->user_id = $request->customer;
        $contract->type_id = $request->type;
        $contract->title = $request->title;
        $contract->sign_date = Carbon::instance(Verta::parseFormat('Y/m/d', $request->sign_date)->datetime());
        $contract->start_date = Carbon::instance(Verta::parseFormat('Y/m/d', $request->start_date)->datetime());
        $contract->end_date = Carbon::instance(Verta::parseFormat('Y/m/d', $request->end_date)->datetime());
        $contract->description = $request->description;
        $contract->contract_code = $request->code;

//        return $request->progress;
//		$contract->progress      = $request->progress;
        DB::beginTransaction();
        try {
            $contract->save();
            foreach ($request->progress as $progress) {
                $cp = new ContractProgress();
                $cp->contract_id = $contract->id;
                $cp->base_progress_id = $progress;
                $cp->save();
                $sp = new SubProgress();
                $sub = $sp::where('base_progress_id', $progress)->get();
                foreach ($sub as $item) {
                    $csp = new ContractSubProgress();
                    $csp->sub_progress_id = $item->id;
                    $csp->contract_id = $contract->id;
                    $csp->save();
                }

            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => ['csp' => $request]], Response::HTTP_CREATED);
//			return response()->json(['message'=>'error on save data'],400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contract = new Contract();
        $data = $contract->with(['ancillary:id,contract_id,contract_code,title', 'type:id,title', 'customer',  'base_progress','sub_progress' , 'checklists' , 'checklistContract'])
            ->findOrFail($id);

        if ($data) {
            return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
        }

        return response()->json(['message' => __('scrum.api.not_found'), Response::HTTP_NOT_FOUND]);


    }

    public function generateCode()
    {
        $str = Str::uuid();
        $contract = new Contract();
        $get = $contract->get(['contract_code'])->where('contract_code', $str);

        /* if ($get) {
			 return $this->generateCode();
		 }*/

        return response()->json(['message' => __('scrum.api.code_generated'), 'code' => $str], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $c = new Contract();
        $contract = $c->find($id);
//        $ids = [];
        DB::beginTransaction();
        $cp = new ContractProgress();
        $csp = new ContractSubProgress();
        $cp->where('contract_id', $id)->delete();
        $csp->where('contract_id', $id)->delete();
        try {
            $insert_array = [];
            foreach ($request->softwares as $software) {
                $allCp = \App\Models\BaseProgress::where('software_id', $software)->get('id');
                foreach ($allCp as $progress) {
                    $insert_array[] = ['base_progress_id' => $progress['id'], 'contract_id' => $id];
                    $csp_arr = [];
                    $sp = new SubProgress();
                    $sub = $sp::where('base_progress_id', $progress['id'])->get();

//                    return $sub;
                    foreach ($sub as $item) {
                        $csp_arr[] = ['sub_progress_id' => $item->id, 'contract_id' => $id];
                    }
                    $csp->insert($csp_arr);
                }
            }

            $cp->insert($insert_array);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

//        return $ids;

//        return $insert_array;

        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $this->show($id)->original['data']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function ChangeSubProgressStatus(Request $request)
    {

        /* $request->validate([
			 'sub_progress_id' => 'required|int',
			 'contract_id'     => 'required|int',
			 'status'          => 'required|in:hold,complete,running,cancel'
		 ]);*/

        $sp_id = $request->sub_progress_id;
        $status = $request->status;
        $cid = $request->contract_id;


        $pivot = new ContractSubProgress();
        $data = $pivot->where('contract_id', $cid)->where('sub_progress_id', $sp_id)->latest('id')->first();

        $data->status = $status;
        $data->save();

//        $contract = new Contract();
//        $data     = $contract->findOrFail($cid);
//        $data = $contract->sub_progress()->attach(
//            [
//                $cid=>
//                [
//                    'status'          => $status,
//                    'sub_progress_id' => $sp_id,
//                ]
//            ]
//        );

        return response()->json(['message' => __('scrum.api.update_success', ['item' => __('scrum.string.status')]), 'data' => $data]);

    }

    public function ChangeBaseProgressStatus(Request $request)
    {
        $bp_id = $request->base_progress_id;
        $status = $request->status;
        $cid = $request->contract_id;
        $pivot = new ContractProgress();
//        return $request->all();
        $data = $pivot->where('contract_id', $cid)->where('base_progress_id', $bp_id)->latest('id')->first();

        $data->status = $status;
        $data->save();

        return response()->json(['message' => __('scrum.api.update_success', ['item' => __('scrum.string.status')]), 'data' => $data]);

    }

    public function updateDate( Request $request,Contract $contract ) {

        $newDate = $request->get('newDate');
        $field = $request->get('dateField');

        $value = Carbon::instance(Verta::parseFormat('Y-m-d',$newDate)->datetime());
        switch ($field) {

            case 'Start':
            case 'start':
                $contract->start_date = $value;
                $contract->save();
                break;
            case 'End':
            case 'end':
                $contract->end_date = $value;
                $contract->save();
                break;
            case 'Sign':
            case 'sign':
                $contract->sign_date = $value;
                $contract->save();
                break;
            default :
                break;
        }

        return response()->json(['message' => __('scrum.api.update_success', ['item' => __('scrum.string.status')]), 'data' => $contract]);
    }

    public function UpdateDomain(Request $request , Contract $contract) {
        $contract->domain_link = $request->domain_link;
        $contract->save();
        return response()->json(['message' => __('scrum.api.update_success', ['item' => __('scrum.string.status')]), 'data' => $contract]);
    }

    public function UpdateThemeLink(Request $request , Contract $contract) {
        $contract->theme_link = $request->theme_link;
        $contract->save();
        return response()->json(['message' => __('scrum.api.update_success', ['item' => __('scrum.string.status')]), 'data' => $contract]);
    }

    public function countMonthContracts(Request $request) {
        $startDate = new DateTime($request['startDate']);
        $endDate   = new DateTime($request['endDate']);
        if($request['startDate'] != null)
            $startDate = Carbon::instance(Verta::parseFormat('Y/m/d', $startDate->format('Y/m/d'))->datetime());
        else
          $startDate = Carbon::now()->subYear();
        if($request['endDate'] != null)
            $endDate = Carbon::instance(Verta::parseFormat('Y/m/d', $endDate->format('Y/m/d'))->datetime());
        else
           $endDate = Carbon::now();
        $contracts = Contract::select('id', 'sign_date')
            ->where('sign_date' , '>=' , $startDate)
            ->where('sign_date' , '<=' , $endDate)
            ->orderBy('sign_date' , 'desc')
            ->get()
            ->groupBy(function($contract) {
                $month = explode('-',$contract->sign_date);
                return $month[0].'-'.$month[1]; // grouping by months
            });
        $contractCount = [];
        $contractYear = [];
        $i =  0 ;
        foreach ($contracts as $key => $value) {
            $contractYear[$i] = $key;
            $contractCount[$i] = count($value);
            $i++ ;
        }
        return response()->json(['message' => __('scrum.api.get_success'),'contractYears' => $contractYear , 'contractCount' => $contractCount], Response::HTTP_OK);
    }
}
