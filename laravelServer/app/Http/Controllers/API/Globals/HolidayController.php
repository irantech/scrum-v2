<?php

namespace App\Http\Controllers\API\Globals;


use App\Http\Controllers\Controller;
use App\Http\Resources\API\Globals\holidayCollection;
use App\Models\holiday;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('scope:manage-holidays')->only(['store' , 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = holiday::all();
//        $data = new holidayCollection($holidays);
        return response()->json($holidays, Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

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
        $section = new holiday();

        $section->date      = $request->date;
        $section->title     = $request->title;

        $section->save();

        return response()->json(['message' => 'holiday added successfully']);
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
    public function update(Request $request, $holiday)
    {
        $holiday = holiday::find($holiday);

        $holiday->date  = $request->date ;
        $holiday->title = $request->title ;
        $holiday->save();

        return response()->json(['message' => 'section updated successfully' , 'data' => $holiday]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = holiday::where('id' , $id)->delete();
        return response()->json(['message' => 'holiday removed successfully']);

    }
}
