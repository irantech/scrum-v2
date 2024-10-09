<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\User\PermissionCollection;
use App\Http\Resources\API\User\Permission as PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function __construct() {
        $this->middleware('scopes:create-permission')->only('store');
        $this->middleware('scopes:update-permission')->only('update');
        $this->middleware('scopes:delete-permission')->only('destroy');
        $this->middleware('scopes:restore-permission')->only('restore');
        $this->middleware('scopes:show-permission')->only(['index' , 'show']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::withTrashed()->orderBy('created_at' , 'desc')->get();
        $json_permissions = new PermissionCollection($permissions);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $json_permissions]);
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
        $request->validate( [
            'title'         => 'required',
        ]);

        $permission = new Permission();
        $permission->title = $request['title'];
        $permission->save();
        $json_permission = new PermissionResource($permission);

        return response()->json([
            'success' => true,
            'data'   => $json_permission ,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
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
    public function update(Request $request, Permission  $permission)
    {
        $request->validate( [
            'title'         => 'required',
        ]);

        $permission->title = $request['title'] ;
        $permission->save();

        return response()->json( [
            'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.permission', 1 ) ] ),
            'data'    => $permission
        ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.permission', 1 ) ] ) ] );
    }

    public function restore($id)
    {
        $permission = Permission::withTrashed()->findOrFail( $id );
        $permission->restore();
        return response()->json( [ 'message' => __( 'scrum.api.restore_success', [ 'item' => trans_choice( 'scrum.title.permission', 1 ) ] ) ], Response::HTTP_OK );
    }

    public function ChangePreDefined(Request $request ,$id)
    {
        $permission = Permission::findOrFail($id) ;
        $request['status'] == true ? $permission->pre_defined = 1 : $permission->pre_defined = 0 ;
        $permission->save() ;
        return response()->json( [ 'message' => __( 'scrum.api.changeStatus_success', [ 'item' => trans_choice( 'scrum.title.permission', 1 ) ] ) ], Response::HTTP_OK );
    }
}
