<?php

namespace App\Http\Controllers\API;

use App\Models\Ancillary;
use App\Models\Contract;
use App\Models\Curl;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\Storage;

class CurlController extends Controller
{
    public function getAncillaryTable()
    {
        $ancillary = new Ancillary();

        return $ancillary->getTable();
    }

    public function index()
    {
        $curl = new Curl();

        return $curl->getTable();
    }

    public function newCurl(Request $request)
    {
        $curl = new Curl();
        $request->merge(['ip' => $request->ip()]);
        $curl->request_data = json_encode($request->all(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $curl->ip = $request->ip();
        $curl->method = $request->method();
        $curl->reference = $request->server('REQUEST_URI');
        $curl->save();
    }

    public function newContract(Request $request)
    {

        $customer = new Customer();
        $contract = new Contract();

        $oldIdCustomer = $customer::where('old_number', $request->gharardad_no)->first();
        $contract->title = $request->gharardad_title;
        $contract->description = '';
        $contract->contract_code = $request->gharardad_no;
        $contract->sign_date = Carbon::today();
        $contract->start_date = Carbon::today();
        $contract->end_date = Carbon::today()->addMonth();
        $contract->save();

        return response()->json(['message' => 'success', 'data' => $oldIdCustomer], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function editContract(Request $request)
    {
        $id = $request->id;
        $number = $request->number;
        $type_id = $request->type_garardad;
        $id_customer = $request->id_customer;
        $manager = $request->manager;
        $address = $request->address;
        $user = $request->user;
        $pass = $request->pass;
        $whmcs_id = $request->whmcs_id;
        $whmcs_hash = $whmcs_id ? md5($request->whmcs_id) : null;

        $customer = new Customer();
        $contract = new Contract();

        $customer = $customer::where('old_number', $number)->first();
        $contract = $contract::where('contract_code', $number)->first();

        $customer->old_id_customer = $id_customer;
        $customer->old_whmcs_id = $whmcs_id;
        $customer->old_whmcs_hash = $whmcs_hash;

        $contract->old_id_customer = $id_customer;
        $contract->type_id = $type_id;
        DB::beginTransaction();
        try {
            $customer->save();
            $contract->save();

        } catch (\Exception $e) {
            DB::rollBack();
        }

        DB::commit();

        return response()->json([
            'message' => 'success',
            'data' => [
                'request' => $request->all(),
                'contract' => $contract,
                'customer' => $customer,
            ]
        ]);

    }

    public function editCustomer(Request $request)
    {

//        return $request->all();

        $hid = $request->hid;
        $customer_number = $request->customer_number;
        $whmcs_id = $request->whmcs_id;
        $whmcs_hash = $whmcs_id ? md5($request->whmcs_id) : null;
        $manager = $request->manager;
        $address = $request->address;
        $phone_number = $request->phone_number;
//        $phone_number    = '09129409530';

        $customer = new Customer();
        $getCustomer = $customer::where('old_number', $customer_number)->first();


        if ($getCustomer) {
            $getCustomer->old_id_customer = $hid;
//            $customer->email           = $request->email;
            $getCustomer->old_whmcs_id = $whmcs_id;
            $getCustomer->old_whmcs_hash = $whmcs_hash;
            $getCustomer->name = $manager;
            $getCustomer->address = $address;
            $getCustomer->$phone_number = $phone_number;
            $getCustomer->save();
        } else {
//            $customer->email           = $request->email;
            $customer->old_id_customer = $hid;
            $customer->old_number = $customer_number;
            $customer->name = $manager;
            $customer->address = $address;
            $customer->old_whmcs_id = $whmcs_id;
            $customer->old_whmcs_hash = $whmcs_hash;
            $customer->phone_number = $phone_number;
            $customer->save();
        }


//        $id = $request->id;
//        $number = $request->number;
//        $id_customer = $request->id_customer;
//        $manager = $request->manager;
//        $user = $request->user;
//        $pass = $request->pass;
//        $whmcs_id = $request->whmcs_id;
//        $whmcs_hash = md5($request->whmcs_id);
//
//        $customer = new Customer();
//
//        $customer = $customer::where('old_number',$number)->first();
//        $customer->old_id_customer = $id_customer;
//        $customer->old_whmcs_id = $whmcs_id;
//        $customer->old_whmcs_hash = $whmcs_hash;
//        $customer->save();

        $this->newCurl($request);

        return response()->json(['message' => 'success', 'data' => $request->all()]);

    }

    public function newCustomer(Request $request)
    {
        $customer = new Customer();
        $getCustomer = $customer::where('email', $request->customer_email)->first();
        if ($getCustomer) {
            $getCustomer->name = $request->customer_name;
            $customer->phone_number = $request->phone_number;
//            $customer->phone_number = '09129409530';
            $getCustomer->manager = $request->manager;
            $getCustomer->address = $request->address;
            $getCustomer->save();
        } else {
            $customer->email = $request->customer_email;
            $customer->name = $request->customer_name;
            $customer->old_number = $request->gharardad_no;
            $customer->phone_number = $request->phone_number;
//            $customer->phone_number = '09129409530';
            $getCustomer->manager = $request->manager;
            $getCustomer->address = $request->address;
            $customer->save();
        }

        return response()->json(['message' => 'customer added', 'data' => $customer]);
    }

    public function newPreContract(Request $request)
    {
        $request->validate([
            'customer_email' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'gharardad_no' => 'required',

            'gharardad_title' => 'required',


        ]);
        $this->newCustomer($request);
        $this->newContract($request);
        $this->newCurl($request);

//        DB::beginTransaction();
//        try {
//        } catch (\Exception $e) {
//            DB::rollBack();
//            throw $e;
//        }
//        DB::commit();
        return response()->json(['done']);
    }

    public function importPrevData(Request $request)
    {
//        return $request;
        $c = $request->customer;
        $ct = $request->contracts;

        try {
            if ($c['id_whmcs']) {
                $customer = new Customer();
                $getCustomer = $customer::where('old_whmcs_id', $c['id_whmcs'])->first();


                if ($getCustomer) {
//            return ['get success'];
                    $getCustomer->old_id_customer = $c['id_customer'];
                    $getCustomer->name = $c['name'];
                    $getCustomer->phone_number = $c['phone_number'];
//                    $getCustomer->phone_number    = '09129409530';
                    $getCustomer->manager = $c['manager'];
                    $getCustomer->address = $c['address'];
                    $getCustomer->save();

                } else {
                    $customer = new Customer();
//            return ['new success'];
                    $customer->old_whmcs_id = $c['id_whmcs'];
                    $customer->old_id_customer = $c['id_customer'];
                    $customer->old_whmcs_hash = md5($c['id_whmcs']);
                    $customer->name = $c['name'];
                    $customer->phone_number = $c['phone_number'];
//                    $customer->phone_number    = '09129409530';
                    $customer->manager = $c['manager'];
                    $customer->address = $c['address'];
                    /*if ($c['email'] != '') {
						$customer->email = $c['email'];
					}*/
                    $customer->save();
                }
//            return ['id_whmcs success'];
            }

            foreach ($ct as $item) {
                $contract = new Contract();
                $date = str_replace('/', '-', $item['jalali_date']);
                $verta = Verta::parse($date);
                $carbonDate = Carbon::instance($verta->DateTime())->toDateString();
                $carbonEnd = Carbon::instance($verta->DateTime())->addDays(10)->toDateString();
                $getContract = $contract::where('contract_code', $item['contract_code'])->first();
//            return $getContract;
                if ($getContract) {
//                    $getContract->sign_date       = $carbonDate;
//                    $getContract->start_date      = $carbonDate;
//                    $getContract->end_date        = $carbonDate;
                    $getContract->title = $item['title'];
                    $getContract->description = $item['description'];
                    $getContract->old_id_customer = $c['id_customer'];
                    $getContract->save();
                } else {
                    $contract->sign_date = $carbonDate;
                    $contract->start_date = $carbonDate;
                    $contract->end_date = $carbonEnd;
                    $contract->title = $item['title'];
                    $contract->contract_code = $item['contract_code'];
                    $contract->description = $item['description'];
                    $contract->old_id_customer = $c['id_customer'];
                    $contract->save();
                }
                $getContract = $contract::orderBy('id', 'DESC')->where('contract_code', $item['contract_code'])->first();
                $ancillaries = $item['ancillary'];


                foreach ($ancillaries as $ancillaryGet) {
                    $ancillary = new Ancillary();
                    $getAncillary = $ancillary::where(['title' => $ancillaryGet['title'], 'contract_code' => $ancillaryGet['contract_code']])->first();
                    if ($getAncillary) {
                        $getAncillary->title = $ancillaryGet['title'];
                        $getAncillary->contract_id = $getContract['id'];
                        $getAncillary->contract_code = $getContract['contract_code'];
                        $getAncillary->save();
                    } else {
                        $ancillary->title = $ancillaryGet['title'];
                        $ancillary->contract_id = $getContract['id'];
                        $ancillary->contract_code = $getContract['contract_code'];
                        $ancillary->save();
                    }
                }

            }
            DB::commit();

        } catch (\Exception $e) {
            return $e;
//            return DB::rollBack();
        }

        return response()->json(['success' => true, 'message' => __('scrum.api.insert_success')]);

    }

    public function get()
    {
        $contract = new Contract();
        return response()->json(['success' => true, 'message' => __('scrum.api.insert_success'), 'data' => ['contracts' => $contract::with('ancillary')->get()]]);
    }


}
