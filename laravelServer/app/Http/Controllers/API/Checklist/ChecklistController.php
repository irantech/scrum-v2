<?php

namespace App\Http\Controllers\API\Checklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChecklistRequest;
use App\Http\Resources\API\Checklist\ChecklistCollection;
use App\Http\Resources\API\Checklist\Checklist as ChecklistResource;
use App\Models\Checklist;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChecklistController extends Controller
{
    public function __construct() {
        $this->middleware('scopes:create-checklist')->only('store');
        $this->middleware('scopes:update-checklist')->only('update');
        $this->middleware('scopes:delete-checklist')->only('destroy');
        $this->middleware('scopes:restore-checklist')->only('restore');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checklists = Checklist::all() ;
        $data = new ChecklistCollection($checklists);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
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
    public function store(ChecklistRequest $request)
    {
         $checklist = new Checklist();
         $checklist->title = $request->title;
         $checklist->description = $request->description;
         $checklist->language_id  = $request->language;
         $checklist->sections = $request->sections;
         $checklist->save();
         $checklist = new ChecklistResource($checklist);
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $checklist], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checklist = Checklist::findOrFail($id);
        if($checklist) {
            $checklist = new ChecklistResource($checklist);
            return response()->json(['message' => __('scrum.api.get_success'), 'data' => $checklist], Response::HTTP_OK);
        }
        else
            return response()->json( [
                'message' => __( 'scrum.api.not_found' ),
                'title'   => trans_choice( 'scrum.title.checklist', 1 )
            ], Response::HTTP_NOT_FOUND, [], JSON_UNESCAPED_UNICODE );
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
    public function update(ChecklistRequest $request, Checklist $checklist)
    {
        $checklist->title = $request->title;
        $checklist->description = $request->description ;
        $checklist->language_id  = $request->language;
        $checklist->sections = $request->sections;
        $checklist->save();
        $checklist = new ChecklistResource($checklist);

        return response()->json( [
            'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.checklist', 1 ) ] ),
            'data'    => $checklist
        ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Checklist $checklist)
    {
        $checklist->delete();
        return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.checklist', 1 ) ] ) ] );
    }

    public function restore($id){
        $checklist = Checklist::withTrashed()->findOrFail($id);
        $checklist->restore();
        return response()->json( [ 'message' => __( 'scrum.api.restore_success', [ 'item' => trans_choice( 'scrum.title.checklist', 1 ) ] ) ], Response::HTTP_OK );
    }
}
