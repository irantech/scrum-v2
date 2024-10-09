<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class UserMetaController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
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
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
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
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
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

	public function saveAvatar(Request $request) {
        $messages = array(
            'required'    => 'انتخاب فایل الزامی است.' ,
            'string'      => 'فرمت نادرست است.'
        );
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|string',
        ] , $messages);

        if ($validator->fails()) {
            return response()->json([ 'error' => [ 'message' => $validator->errors()->all() ]], Response::HTTP_UNPROCESSABLE_ENTITY );
        }
	    $meta = UserMeta::where('user_id' , Auth::user()->id)->where('key' , 'avatar')->first();
	    if($meta){
            $meta->value = $request->avatar;
            $meta->save() ;
        }
	    else {
            $meta = new UserMeta();
            $meta->user_id = Auth::user()->id;
            $meta->key = 'avatar' ;
            $meta->value = $request->avatar;
            $meta->save() ;
        }
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $meta] , Response::HTTP_OK);
    }


	public function saveSignature(Request $request) {
        $messages = array(
            'required'    => 'انتخاب فایل الزامی است.' ,
            'string'      => 'فرمت نادرست است.'
        );
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ] , $messages);

        if ($validator->fails()) {
            return response()->json([ 'error' => [ 'message' => $validator->errors()->all() ]], Response::HTTP_UNPROCESSABLE_ENTITY );
        }
	    $meta = UserMeta::where('user_id' , Auth::user()->id)->where('key' , 'signature')->first();
	    if($meta){
            $meta->value = $request->signature;
            $meta->save() ;
        }
	    else {
            $meta = new UserMeta();
            $meta->user_id = Auth::user()->id;
            $meta->key = 'signature' ;
            $meta->value = $request->signature;
            $meta->save() ;
        }
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $meta] , Response::HTTP_OK);
    }
}
