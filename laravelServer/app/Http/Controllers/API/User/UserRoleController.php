<?php

namespace App\Http\Controllers\API\User;

use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$roles = new UserRole();
		$data  = $roles::all(['id', 'title', 'description']);

		return response()->json($data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
//        return $request;
		$request->validate([
			'title' => 'required|unique:user_roles,title',
		]);
		$role              = new UserRole();
		$role->title       = $request->title;
		$role->description = $request->description;
		$role->save();

		return response()->json(['message' => 'user role created', 'data' => ['title' => $request->title,
		]], 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$role    = new UserRole();
		$role_data = $role->findOrFail($id);

		return response()->json($role_data);
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
		$role = new UserRole();
		$request->validate([
			'title' => 'required|unique:' . $role->getTable() . ',title,' . $id,
		]);

		$role->find($id);
		$role->title       = ($request->title) ? $request->title : $role->title;
		$role->description = ($request->description) ? $request->description : $role->description;

//        return $request->title;
//        return var_dump($request->headers);

//        $role->save();
		$role->where('id', $id)->update(['title' => $role->title, 'description' => $role->description]);

		return response()->json(['message' => 'role updated', 'data' => $role]);

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$role = new UserRole();
		$role->findOrFail($id);

		$role::where('id', $id)->delete();

		return response()->json(
			['message' => 'User Role deleted']);
	}
}
