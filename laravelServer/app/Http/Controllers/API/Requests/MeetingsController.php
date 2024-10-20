<?php

namespace App\Http\Controllers\API\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingStoreRequest;
use App\Http\Resources\API\Meeting\MeetingCollection;
use App\Models\Meeting;
use App\Models\MeetingDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MeetingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('scopes:create_meeting')->only('store');
        $this->middleware('scopes:index_meeting')->only('index');
        $this->middleware('scopes:show_meeting')->only('show');
        $this->middleware('scopes:update_meeting')->only('update');
    }

    public function update(Request $request, Meeting $meeting)
    {
        $data = [];
        $user = Auth::user();
        $meetings_id = $user->meetings->pluck('id')->toArray();
        if (in_array($meeting->id, $meetings_id)) {
            $meeting->update([
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
            $meeting1 = Meeting::find($meeting->id);
            $data = new \App\Http\Resources\API\Meeting\Meeting($meeting1);
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function show(Meeting $meeting)
    {
        $data = [];
        $user = Auth::user();
        $meetings_id = $user->meetings->pluck('id')->toArray();
        if (in_array($meeting->id, $meetings_id))
            $data = new MeetingCollection($meeting->with('user')->where('id', $meeting->id)->get());
        return response()->json($data, Response::HTTP_OK);
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->role()->first()->title == 'admin')
            $meetings = Meeting::with('user')->get();
        elseif ($user->role()->first()->type == 'manager') {
            $meetings = MeetingDetail::with(['meeting', 'user'])->where('user_id', $user->id)->get();
        } else
            $meetings = $user->meetings()->get();
        $data = new MeetingCollection($meetings);
        return response()->json($data, Response::HTTP_OK);
    }

    public function store(MeetingStoreRequest $request)
    {
        $meeting = Meeting::create([
            'subject' => $request->subject,
            'description' => $request->description,
            'user_id' => Auth::user()->id,
        ]);
        $data = new \App\Http\Resources\API\Meeting\Meeting($meeting);
        return response()->json($data, Response::HTTP_OK);
    }

}
