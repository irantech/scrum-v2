<?php

namespace App\Http\Controllers\API\Agency;

use App\Http\Controllers\Controller;
use App\Notifications\notToAgenciesNotification;
use Illuminate\Http\Request;
use App\Models\Agency;
use App\Models\AgencyInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;


class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $agency_info = Agency::with('agencyinfo')->paginate($request->input('per_page'));
            $total_count = Agency::with('agencyinfo')->get()->count();
            return response()->json(['info' => $agency_info, 'count' => $total_count]);
        } catch (QueryException $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        foreach ($data as $item){

            //agency main data
                $agency = Agency::where('irantech_agency_id', $item['id'])->where('type', $item['type'])->first();

                if (!$agency) {
                    // If the record doesn't exist, create it
                    $agencyData = [
                        'irantech_agency_id' => $item['id'],
                        'manager_name' => $item['name_manager'],
                        'agency' => $item['nama_agency'],
                        'type' => $item['type']
                    ];
                    $agency = Agency::create($agencyData);
                }

                //agency info

                $agency_info = [];
                foreach ($item as $key => $val) {
                    if (in_array($key, ['id', 'name_manager', 'nama_agency' , 'type'])) {
                        continue;
                    }
                    $agency_info[] = [
                        'label' => $key,
                        'info' => $val,
                    ];
                }

                $agency->AgencyInfo()->createMany($agency_info);


        }

        return response()->json(['message' => 'agency_info_sent']);

    }


    public function useApi($apiEndpoint,$postData)
    {
        // Make the POST request using the Laravel HTTP client
        $response = Http::withHeaders([
            'Content-Type' => 'application/json', // Adjust the content type according to the API requirements
        ])->post($apiEndpoint, $postData);
        return $response->json();
    }


    public function storeAgencies($data)
    {
        if (is_array($data)) {
            foreach ($data as $item) {
                $agency = Agency::where('irantech_agency_id', $item['id'])->first();

                if (!$agency) {
                    // If the record doesn't exist, create it
                    $agencyData = [
                        'irantech_agency_id' => $item['id'],
                        'manager_name' => $item['name_manager'],
                        'agency' => $item['nama_agency'],
                        'type' => $item['type']
                    ];
                    $agency = Agency::create($agencyData);
                }

                // Find the largest row_id in AgencyInfo and add 1
                if (AgencyInfo::max('row_id')) {
                    $largestRowId = AgencyInfo::max('row_id') + 1;
                } else {
                    $largestRowId = 1;
                }

                $agency_info = [];
                foreach ($item as $key => $val) {
                    if (in_array($key, ['id', 'name_manager', 'nama_agency', 'type'])) {
                        continue;
                    }
                    $agency_info[] = [
                        'row_id' => $largestRowId, // Assign the new row_id
                        'label' => $key,
                        'info' => $val,
                    ];
                }

                $agency->AgencyInfo()->createMany($agency_info);
            }
        }
    }


    public function storeTriger(Request $request)
    {

        $request->header('Access-Control-Allow-Origin', '*');
        $request->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $request->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        $apiEndpoint = 'https://www.iran-tech.com/admin28654/order_cost/api/api_recive_post.php';
        $count = $this->useApi($apiEndpoint,['totalCount' => 'fetch']);

        $range = $request->input('range'); // Assuming the request sends the range as 'range' parameter

        if ($range) {
            [$offsetStart, $offsetEnd] = explode('-', $range);
            $limit = 10;

            for ($i = $offsetStart; $i < $offsetEnd; $i += $limit) {
                sleep('2');
                $postData = [
                    'offset' => $i,
                    'limit' => $limit,
                ];
                $data = $this->useApi($apiEndpoint, $postData);
                $this->storeAgencies($data);
            }
        }

        return response()->json($count);

    }
    public function storeTrigerAutomation(Request $request)
    {
        $data = $request->all();
        $this->storeAgencies($data);
        return response()->json('success');
    }

    // ... Rest of the methods remain the same ...

    /**
     * Apply filters to the agency query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $data
     * @return void
     */

    public function showNew(Request $request)
    {
        try {
            $req_data = json_decode($request->getContent(), true);
            $query = Agency::with('agencyinfo');
            $agency_info = $query->get();
            $rows = $this->joinTablesPhp($agency_info);
            $filterd_data = $this->applyPhpFiltersNew($rows, $req_data );

            return response()->json($filterd_data);
        } catch (QueryException $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }
    }
    public function joinTablesPhp($agency_info)
    {
        $rows = array();


        foreach ($agency_info as $agency) {
            $phons_array = [];
            foreach ($agency['agencyinfo'] as $info) {
                if ($info['label'] == 'mob_manager') {
                    if (isset($info['info']) && !empty($info['info'])) {
                        $phons_array[] = $info['info'];
                    }
                }
            }
            $phons_array = array_unique($phons_array);

            foreach ($agency['agencyinfo'] as $info) {
                $rows[$info['row_id']]['agency_id'] = $info['agency_id'];
                $rows[$info['row_id']]['manager_name'] = $agency['manager_name'];
                $rows[$info['row_id']]['agency'] = $agency['agency'];
                $rows[$info['row_id']]['type'] = $agency['type'];
                $rows[$info['row_id']]['mob_manager'] = $phons_array;

                if ($info['label'] == 'lost') {
                    $rows[$info['row_id']]['lost'] = $info['info'];
                }

                if ($info['label'] == 'sites') {
                    $rows[$info['row_id']]['sites'] = $info['info'];
                }
                if ($info['label'] == 'status') {
                    $rows[$info['row_id']]['status'] = $info['info'];
                }
                if ($info['label'] == 'status_date') {
                    $rows[$info['row_id']]['status_date'] = $info['info'];
                }
                if ($info['label'] == 'date_gh') {
                    $rows[$info['row_id']]['date_gh'] = $info['info'];
                }
                if ($info['label'] == 'employee_id') {
                    $rows[$info['row_id']]['employee_id'] = $info['info'];
                }
            }


        }
        return $rows;

    }
    public function applyPhpFiltersNew($rows, $req_data)
    {
        if (isset($req_data['statusType']) && !empty($req_data['statusType'])) {
            $rows = $this->statusTypFilter($req_data['statusType'] , $rows);
        }

        if (isset($req_data['employee']) && !empty($req_data['employee'])) {
            $rows = $this->employeeFilter($req_data['employee'] , $rows);
        }

        if ((isset($req_data['startDate']) || isset($req_data['endDate']) && $req_data['startDate'] || $req_data['endDate'])) {
            $rows = $this->dateFilter($req_data['startDate'] , $req_data['endDate'] , $rows);
        }
        if (isset($req_data['status']) && !empty($req_data['status'])) {
            $rows = $this->statusFilter($req_data['status'] , $rows);
        }
        if (!empty($req_data['name'])) {
            $rows = $this->nameFilter($req_data['name'] , $rows);
        }


        if (!empty($req_data['mob_manager'])) {
            $rows = $this->phoneFilter($req_data['mob_manager'], $rows);
        }
        if ($req_data['type'] != 'all') {
            $rows = $this->typeFilter($req_data['type'] , $rows);
        }

        if (!empty($req_data['lost'])) {
            $rows = $this->lostFilter($req_data['lost'] , $rows);
        }

        if (!empty($req_data['sites'])) {
            $rows = $this->sitesFilter($req_data['sites'] , $rows);
        }

         $rows = $this->repeatedPhoneFilter($rows);
         $rows = $this->deleteExtraItems( $rows ,$req_data['page'] , $req_data['perPage'] );


        return $rows;
    }
    public function dateFilter($startDate, $endDate, $rows)
    {
        $startDate = str_replace('-', '', $startDate);
        $endDate = str_replace('-', '', $endDate);
        foreach ($rows as $key => &$agency) {

                if ($startDate  && $startDate != 'null' && isset($agency['status_date']) && $agency['status_date'] < $startDate) {
                    unset($rows[$key]);
                }
                if ($endDate && $endDate != 'null' &&  isset($agency['status_date']) && $agency['status_date'] > $endDate) {
                    unset($rows[$key]);
                }
        }
        return $rows;
    }
    public function statusTypFilter($status_type , $rows)
    {
        if ($status_type == 'last') {
            $filteredData = [];
            $encounteredNames = [];
            foreach ($rows as $item) {
                $managerId = $item['agency_id'];
//            $statusDate = $item['status_date'];
                $encounteredNames[$managerId] = $item;
            }
            $filteredData = array_values($encounteredNames); // Re-index the array
        } else if ($status_type == 'all') {
            $filteredData = $rows;
        } else if ($status_type == 'record') {
            $new_rows = [];
            foreach ($rows as $key => $val) {
                if (isset($val['date_gh'])) {
                    $val['date_gh'] = str_replace('-', '', $val['date_gh']);
                    $val['date_gh'] = str_replace('/', '', $val['date_gh']);
                    $val['status_date'] = $val['date_gh'];
                    $new_rows[$key] = $val;
                }
            }
            $filteredData = $new_rows;
        }

        return $filteredData;
    }
    public function employeeFilter($employee_id , $rows)
    {
        if ($employee_id != 'all')  {
            foreach ($rows as $key => &$agency) {
                if (!isset($agency['employee_id'])) {
                    unset($rows[$key]);
                } else if ($employee_id != $agency['employee_id']) {
                    unset($rows[$key]);
                }
            }
        }

        return $rows;
    }
    public function statusFilter($status , $rows)
    {
        if (!is_array($status)) {
            $status = explode(',', $status);
        }
        foreach ($rows as $key => &$agency) {
            if (!isset($agency['status'])) {
                unset($rows[$key]);
            } else if (!in_array($agency['status'], $status)) {
                unset($rows[$key]);
            }
        }


        return $rows;
    }
    public function nameFilter($names , $rows){
         $names = explode(',', $names);
        foreach ($names as $name) {
            if (isset($name) and !empty($name)) {
                foreach ($rows as $key => &$agency) {
                    if (strpos($agency['manager_name'], $name) === false) {
                        unset($rows[$key]);
                    }
                }
            }
        }
        return $rows;
    }
    public function sitesFilter($sites , $rows){
         if (isset($sites) and !empty($sites)) {
             if (!is_array($sites)) {
                 $sites = explode(',', $sites);
             }

                foreach ($rows as $key => &$agency) {
                    $delete = true;
                    if (isset($agency['sites']) and !empty($agency['sites'])) {
                        $agancy_sites = json_decode($agency['sites']);
                        if (isset($agancy_sites) and !empty($agancy_sites)) {
                            foreach ($agancy_sites as $mob) {
                                foreach ($sites as $site) {
                                    if (stripos($mob, $site) !== false) {
                                        $delete = false;
                                    }
                                }
                            }
                        }
                    }
                    if ($delete) {
                        unset($rows[$key]);
                    }
                }
            }
        return $rows;
    }
    public function phoneFilter($phone_numbers, $rows)
    {
        $persianNumerals = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $arabicNumerals = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
        $phone_numbers = explode(',', $phone_numbers);
        foreach ($phone_numbers as $phone) {
            if (isset($phone) and !empty($phone)) {
                foreach ($rows as $key => &$agency) {
                    $delete = true;
                    foreach ($agency['mob_manager'] as $mob) {
                        $mob = str_replace($persianNumerals, range(0, 9), $mob);
                        $mob = str_replace($arabicNumerals, range(0, 9), $mob);
                        if (strpos($mob, $phone) === 0) {
                            $delete = false;
                        }
                    }
                    if ($delete) {
                        unset($rows[$key]);
                    }
                }
            }
        }
        return $rows;
    }
    public function repeatedPhoneFilter($rows)
    {
        $prev_numbers = [];
        $persianNumerals = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $arabicNumerals = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
         foreach ($rows as $key => &$agency) {
            $delete = true;
            foreach ($agency['mob_manager'] as $mob) {
                $mob = str_replace($persianNumerals, range(0, 9), $mob);
                $mob = str_replace($arabicNumerals, range(0, 9), $mob);
                if (!in_array($mob, $prev_numbers)) {
                    $delete = false;
                    $prev_numbers[] = $mob;
                }
            }
            if ($delete) {
                unset($rows[$key]);
            }
        }

        return $rows;
    }
    public function typeFilter($type, $rows)
    {
        foreach ($rows as $key => &$agency) {
            if ($type != $agency['type']) {
                unset($rows[$key]);
            }
        }
        return $rows;
    }
    public function lostFilter($lost , $rows)
    {
            $lost = explode(',', $lost);
        foreach ($lost as $val) {
            if (isset($val) and !empty($val)) {
                foreach ($rows as $key => &$agency) {
                    if (!isset($agency['lost']) || $val != $agency['lost']) {
                        unset($rows[$key]);
                    }
                }
            }
        }
        return $rows;
    }
    public function deleteExtraItems( $rows , $page , $perPage): array
    {
        $filteredData = [];
        $encounteredNames = [];
        foreach ($rows as $item) {
            $managerId = $item['agency_id'];
//            $statusDate = $item['status_date'];
            $encounteredNames[$managerId] = $item;
        }
        $filteredData = array_values($encounteredNames); // Re-index the array
        $count = count($filteredData);


        if ($count >= 100) {
            if ($page && $perPage) {
                $startIndex = ($page - 1 ) * $perPage;
                $endIndex = ($page) * $perPage;
                $filteredData = array_slice($filteredData, $startIndex, $endIndex - $startIndex);
            }
        }
        return ['info' => $filteredData , 'count' => $count ];
    }
    public function sms(Request $request)
    {
        try {
            $req_data = json_decode($request->getContent(), true);
            $query = Agency::with('agencyinfo');
            $agency_info = $query->get();
            $rows = $this->joinTablesPhp($agency_info);
            $filterd_data = $this->applyPhpFiltersNew($rows, $req_data )['info'];

            $checkPhonesRepeated = [];
            foreach ($req_data['phoneNumbers'] as $item) {
                if (is_array($item)) {
                    $output = array_merge($output, $item);
                } else {
                    $output[] = $item;
                }
            }
            $req_data['phoneNumbers'] = $output;


            if ($req_data['phoneNumbers'][0] == 'unselect') {
                $receiver = array();
                foreach ($filterd_data as $item) {
                    if ( !in_array($item['mob_manager'][0], $req_data['phoneNumbers'])) {
                        $receiver[] = $item['mob_manager'][0];
                    }
                }
            } else {
                if (is_array($req_data['phoneNumbers'])) {
                    array_shift($req_data['phoneNumbers']);
                    $receiver =  $req_data['phoneNumbers'];
                }

            }

            var_dump($receiver);die();
            $receiver = ['09160814526'];

            if (isset($req_data['massage']) && !empty($req_data['massage'])) {
                $notification_data = $req_data['massage'];
                $Notification = Notification::send($receiver, new notToAgenciesNotification($notification_data));
            }

            return response()->json(['info' => "متن پیامک به شماره ها ارسال شد." ]);
        } catch (QueryException $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }
    }
    public function excel(Request $request)
    {
        try {
            $req_data = json_decode($request->getContent(), true);
            $query = Agency::with('agencyinfo');
            $agency_info = $query->get();
            $rows = $this->joinTablesPhp($agency_info);
            $agency_info_array = $this->applyPhpFiltersNew($rows, $req_data )['info'];

            foreach ($req_data['phoneNumbers'] as $item) {
                if (is_array($item)) {
                    $output = array_merge($output, $item);
                } else {
                    $output[] = $item;
                }
            }
            $req_data['phoneNumbers'] = $output;
             foreach ($agency_info_array as $info) {
                 if (isset($info['mob_manager'][0]) && !empty($info['mob_manager'][0])) {


                         $phoneNumber = $info['mob_manager'][0];


                        if ($req_data['phoneNumbers'][0] == 'unselect') {
                            $isPhoneNumberValid = true;
                        } else {
                            $isPhoneNumberValid = in_array($phoneNumber, $req_data['phoneNumbers']);
                        }



                     if ($isPhoneNumberValid) {
                         $receiver[] = [
                             'phone' => $phoneNumber,
                             'manager' => $info['manager_name'],
                             'agency' => $info['agency']
                         ];
                     }

                  }
                }




            return response()->json(['data' => $receiver  , 'success' => 'ok']);
        } catch (QueryException $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $data = $request->all();
        foreach ($data as $item) {
            $agency = Agency::Where('irantech_agency_id', $item['id'])
                            ->where('type' , $item['type'])
                            ->first();
            if ($agency) {
                $agency->AgencyInfo()->delete();
                $agency->delete();
            }
        }
        return $this->store($request);
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
