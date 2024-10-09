<?php

namespace App\Http\Controllers\API\Globals;

use App\Http\Controllers\Controller;
use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SoftwareController extends Controller
{

    public function __construct() {
        $this->middleware('scopes:create-software')->only('store');
        $this->middleware('scopes:update-software')->only('update');
        $this->middleware('scopes:delete-software')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $software = new Software();
        $data     = $software::all('id', 'title');
        return response()->json($data, Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $software = new Software();
        $request->validate([
        	'title'=>'required|unique:softwares,title'
        ]);
        $software->title = $request->title;
        $software->save();

        return response()->json(['success'=>true,'message'=>__( 'scrum.api.insert_success', [ 'item' => trans_choice( 'scrum.title.software', 1 ) ] )]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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
}
