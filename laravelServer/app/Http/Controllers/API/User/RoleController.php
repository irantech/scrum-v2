<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Auth\RoleCollection;
use App\Http\Resources\API\Auth\Role as RoleResource;
use App\Models\Permission;
use App\Models\role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Activitylog\Models\Activity;

class RoleController extends Controller
{
    public function __construct() {
        $this->middleware('scopes:create-role')->only('store');
        $this->middleware('scopes:update-role')->only('update');
        $this->middleware('scopes:delete-role')->only('destroy');
        $this->middleware('scopes:restore-role')->only('restore');
        $this->middleware('scopes:show-role')->only(['index' , 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::withTrashed()->orderBy('created_at' , 'desc')->get();
        $json_roles = new RoleCollection($roles);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $json_roles]);
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
            'type'          => 'in:employee,customer,manager,admin'
        ]);

        $role = new Role();
        $role->title = $request['title'];
        $role->section_id = $request->section;
        $role->type = $request->type;
        $role->save();
        //$permissions = Permission::where('pre_defined' , 1)->get();
        //$role->permissions()->sync($permissions);
        $josn_role = new RoleResource($role);
        return response()->json([
            'success' => true,
            'data'   => $josn_role ,
            'message' => __( 'scrum.api.insert_success' ),
             ], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $data = new \App\Http\Resources\API\Auth\Role($role);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);
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
    public function update(Request $request, Role $role)
    {
        $request->validate( [
            'title'         => 'required',
            'type'          => 'in:employee,customer,manager,admin'
        ]);

        $role->title = $request['title'] ;
        $role->section_id = $request->section;
        $role->type = $request->type;
        $role->save();
        $role->permissions()->sync($request['permission']);

        return response()->json( [
            'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.role', 1 ) ] ),
            'data'    => new RoleResource($role)
        ] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->users()->update(['role_id' => 7]);
        $role->delete();
        return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.role', 1 ) ] ) ] );
    }

    public function restore($id)
    {
        $role = Role::withTrashed()->findOrFail( $id );
        $role->restore();
        return response()->json( [ 'message' => __( 'scrum.api.restore_success', [ 'item' => trans_choice( 'scrum.title.role', 1 ) ] ) ], Response::HTTP_OK );
    }
}
