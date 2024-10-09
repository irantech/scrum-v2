<?php

namespace App\Http\Controllers\API\Checklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleChecklistRequest;
use App\Http\Resources\API\Checklist\TitleChecklistCollection;
use App\Http\Resources\API\Checklist\TitleChecklist as TitleChecklistResource;
use App\Models\TitleChecklist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TitleChecklistController extends Controller
{
    public function __construct() {
        $this->middleware('scopes:create-titleChecklist')->only('store');
        $this->middleware('scopes:update-titleChecklist')->only('update');
        $this->middleware('scopes:delete-titleChecklist')->only('destroy');
        $this->middleware('scopes:restore-titleChecklist')->only('restore');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $titleChecklist = TitleChecklist::all();
        $data = new TitleChecklistCollection($titleChecklist);
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
    public function store(TitleChecklistRequest $request)
    {
        $titleChecklist = new TitleChecklist();
        $titleChecklist->title = $request->title ;
        $titleChecklist->description = $request->description;
        $titleChecklist->checklist_id = $request->checklist_id;
        $titleChecklist->save();
        $titleChecklist->sections()->sync($request->section);
        $titleChecklist = new TitleChecklistResource($titleChecklist);
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $titleChecklist], Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $titleChecklist = TitleChecklist::findOrFail($id);
        if($titleChecklist) {
            $titleChecklist = new TitleChecklistResource($titleChecklist);
            return response()->json(['message' => __('scrum.api.get_success'), 'data' => $titleChecklist], Response::HTTP_OK);
        }
        else
            return response()->json( [
                'message' => __( 'scrum.api.not_found' ),
                'title'   => trans_choice( 'scrum.title.titleChecklist', 1 )
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
    public function update(TitleChecklistRequest $request, TitleChecklist $titleChecklist)
    {
        $titleChecklist->title = $request->title ;
        $titleChecklist->description = $request->description;
        $titleChecklist->save();
        $titleChecklist->sections()->sync($request->section);
        $titleChecklist = new TitleChecklistResource($titleChecklist);

        return response()->json( [
            'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.titleChecklist', 1 ) ] ),
            'data'    => $titleChecklist
        ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TitleChecklist $titleChecklist)
    {
        $titleChecklist->delete();
        return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.titleChecklist', 1 ) ] ) ] );
    }

    public function restore($id) {
        $titleChecklist = TitleChecklist::withTrashed()->findOrFail($id);
        $titleChecklist->restore();
        return response()->json( [ 'message' => __( 'scrum.api.restore_success', [ 'item' => trans_choice( 'scrum.title.titleChecklist', 1 ) ] ) ], Response::HTTP_OK );
    }
}
