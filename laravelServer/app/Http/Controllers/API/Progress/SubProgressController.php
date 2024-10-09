<?php

namespace App\Http\Controllers\API\Progress;

use App\Http\Controllers\Controller;
use App\Models\SubProgress;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubProgressController extends Controller
{
    public function __construct() {
        $this->middleware('scopes:create-subProgress')->only('store');
        $this->middleware('scopes:update-subProgress')->only('update');
        $this->middleware('scopes:delete-subProgress')->only('destroy');
        $this->middleware('scopes:restore-subProgress')->only('restore');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sp   = new SubProgress();
        $data = $sp::IdDescending()->with('base_progress')->get();
        /*foreach ($data as $item) {
            $item->trashed = $item->trashed();
        }*/

        return response()->json(['message' => 'scrum.api.get_success', 'progress' => $data]);
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
            'section_id'       => 'required|int',
            'base_progress_id' => 'required|int',
            'title'            => 'required',
            'description'      => 'string',
        ]);

        $sp                   = new SubProgress();
        $sp->section_id       = $request->section_id;
        $sp->base_progress_id = $request->base_progress_id;
        $sp->title            = $request->title;
        $sp->description      = $request->description;
//        $progress->estimated_time   = $request->estimated_time;
//        $progress->refer_to         = $request->refer_to;

        $sp->save();

        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $sp]);

    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sp   = new SubProgress();
        $data = $sp::withTrashed()->with(['base_progress'])->first($id);

//        return $data->trashed();

        foreach ($data as $item) {
            $item->trashed = $item->trashed();
        }


        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sp = new SubProgress();
        $request->validate([
            'section_id'       => 'required|int',
            'base_progress_id' => 'required|int',
            'title'            => 'required',
            'description'      => 'string',
        ]);
        $sub                   = $sp->findOrFail($id);
        $sub->section_id       = $request->section_id;
        $sub->base_progress_id = $request->base_progress_id;
        $sub->title            = $request->title;
        $sub->description      = $request->description;
        $sub->save();

        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $sp]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sp = SubProgress::findOrFail($id);
        $sp->delete();
        return response()->json(['message' => __('scrum.api.remove_success', ['item' => trans_choice('scrum.title.sub_progress', 1)])]);
    }

    public function restore($id)
    {
        $sp = SubProgress::withTrashed()->findOrFail($id);
        $sp->restore();

        return response()->json(['message' => __('scrum.api.restore_success', ['item' => trans_choice('scrum.title.sub_progress', 1)])], Response::HTTP_OK);

    }
}
