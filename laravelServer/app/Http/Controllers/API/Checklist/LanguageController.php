<?php

namespace App\Http\Controllers\API\Checklist;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Checklist\LanguageCollection;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\API\Checklist\Language as LanguageResource;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $languageList = Language::all();
        $data = new LanguageCollection($languageList);
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
    public function store(Request $request)
    {
        $request->validate( [
            'title'         => 'required|string',
        ] );
        $language = new Language();
        $language->title = $request->title;
        $language->save();
        $data = new LanguageResource($language);
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $data], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Language $language)
    {
        $data = new LanguageResource($language);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
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
    public function update(Request $request, Language $language)
    {
        $request->validate( [
            'title'         => 'required|string',
        ] );
        $language->title = $request->title;
        $language->save();
        $data = new LanguageResource($language);
        return response()->json(
            ['message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.language', 1 ) ] ) , 'data' => $data], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Language  $language
     * @return \Illuminate\Http\Response
     */
    public function destroy(Language $language)
    {
        $language->delete();
        return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.language', 1 ) ] ) ] );
    }
}
