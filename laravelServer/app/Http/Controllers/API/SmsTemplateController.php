<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as LanguageResource;
use Illuminate\Http\Response;

class SmsTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $smsChannels = SmsTemplate::all();
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $smsChannels], Response::HTTP_OK);
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
            'title'         => 'required|string',
            'key'           => 'required' ,
            'template'      => 'required'
        ] );

        $SmsTemplate = SmsTemplate::create($request->all());
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $SmsTemplate], Response::HTTP_OK);
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
    public function update(Request $request, SmsTemplate $smsTemplate)
    {
        $request->validate( [
            'title'         => 'required|string',
            'key'           => 'required' ,
            'template'      => 'required'
        ] );

        $smsTemplate->update($request->all());
        $SmsTemplate = $smsTemplate->find($smsTemplate->id);
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $SmsTemplate], Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SmsTemplate $smsTemplate)
    {
        $smsTemplate->delete();
        return response()->json( [ 'message' => __( 'scrum.api.remove_success' ) ] );
    }
}
