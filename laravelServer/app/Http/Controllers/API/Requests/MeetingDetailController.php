<?php

namespace App\Http\Controllers\API\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingsDetailsStoreRequest;
use App\Http\Resources\API\MeetingDetails\IndexCollection;
use App\Http\Resources\API\MeetingDetails\IndexResource;
use App\Http\Resources\API\MeetingDetails\MeetingDetailsCollection;
use App\Http\Resources\API\MeetingDetails\ShowResource;
use App\Http\Resources\API\MeetingDetails\StoreResource;
use App\Http\Resources\API\MeetingDetails\UpdateResource;
use App\Models\Meeting;
use App\Models\MeetingDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MeetingDetailController extends Controller
{

    public function __construct()
    {
        $this->middleware('scopes:create_meeting_details')->only('store');
        $this->middleware('scopes:index_meeting_details')->only('index');
        $this->middleware('scopes:show_meeting_details')->only('show');
        $this->middleware('scopes:update_meeting_details')->only('update');
    }


    public function update(Request $request, MeetingDetail $meetingDetail)
    {
        $user = Auth::user();
        $meeting_details_id = $user->meetingDetails()->pluck('id');
        $data = [];
        if (($user->role()->first()->type == 'manager' || $user->role()->first()->title == 'admin') && in_array($meetingDetail->id, $meeting_details_id)) {
            $meetingDetail->update([
                'approval_status' => $request->approval_status,
                'description' => $request->description,
                'date_time' => $request->date_time,
//            'user_id' => Auth::user()->id,
            ]);
            $meeting_detail = MeetingDetail::find($meetingDetail->id);
            Meeting::findOrFail($meeting_detail->meeting_id)->update([
                'result' => $request->result,
                'meeting_with_superadmin' => $request->meeting_with_superadmin,
            ]);
            $meeting_detail2 = $meeting_detail->with(['meeting', 'user'])->where('id', $meeting_detail->id)->get();
            $data = new MeetingDetailsCollection($meeting_detail2);
        }
        return response()->json($data, Response::HTTP_OK);
    }


    public function show(MeetingDetail $meetingsDetails)
    {
        $data = [];
        $user = Auth::user();

        $meetings = $user->meetings()
            ->with('meetingDetails')
            ->get()
            ->flatMap(function ($meeting) {
                return $meeting->meetingDetails->pluck('meeting_id');
            })->toArray();

        $meetings_id = $user->meetingDetails()->pluck('meeting_id')->toArray();

        if ($user->role()->first()->title == 'admin' || in_array($meetingsDetails->meeting_id, $meetings_id) || in_array($meetingsDetails->meeting_id, $meetings)) {
            $meeting_detail = MeetingDetail::with(['meeting', 'user'])->where('id', $meetingsDetails->id)->get();
            $data = new MeetingDetailsCollection($meeting_detail);
        }
        return response()->json($data, Response::HTTP_OK);
    }


    public function index()
    {
        $user = Auth::user();
        if ($user->role()->first()->title == 'admin')
            $meeting_details = MeetingDetail::with(['meeting', 'user'])->get();
        elseif ($user->role()->first()->type == 'manager') {
            $meeting_id = $user->meetingDetails()->pluck('meeting_id')->toArray();
            $meeting_details = MeetingDetail::whereIn('meeting_id', $meeting_id)->with('meeting', 'user')->get();
        } else {
            $meetings = $user->meetings()
                ->with('meetingDetails')
                ->get()
                ->flatMap(function ($meeting) {
                    return $meeting->meetingDetails->pluck('meeting_id');
                })->toArray();
            $meeting_details = MeetingDetail::whereIn('meeting_id', $meetings)->with('meeting', 'user')->get();

        }
        $data = new MeetingDetailsCollection($meeting_details);
        return response()->json($data, Response::HTTP_OK);
    }


    public function store(MeetingsDetailsStoreRequest $request)
    {
        $meetingDetail = MeetingDetail::create([
            'approval_status' => $request->approval_status,
            'description' => $request->description,
            'date_time' => $request->date_time,
            'meeting_id' => $request->meeting_id,
            'user_id' => Auth::user()->id,
        ]);
        Meeting::findOrFail($request->meeting_id)->update([
            'result' => $request->result,
            'meeting_with_superadmin' => $request->meeting_with_superadmin,
        ]);
        $meeting_detail1 = $meetingDetail->with(['meeting', 'user'])->where('id', $meetingDetail->id)->get();
        $data = new MeetingDetailsCollection($meeting_detail1);
        return response()->json($data, Response::HTTP_OK);
    }


}
