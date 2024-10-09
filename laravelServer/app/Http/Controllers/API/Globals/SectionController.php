<?php

namespace App\Http\Controllers\API\Globals;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SectionController extends Controller
{
	protected $table;

	public function __construct()
	{
		$section     = new Section();
		$this->table = $section->getTable();
        $this->middleware('scopes:create-section')->only('store');
        $this->middleware('scopes:admin-edit-sections')->only('update');
        $this->middleware('scopes:delete-section')->only('destroy');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$sections = Section::orderBy('order' , 'ASC')->get();
		$data = new \App\Http\Resources\API\Globals\SectionCollection($sections);

        return response()->json($data, Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
		$section = new Section();

		$request->validate([
			'title'       => "required|max:50|unique:$this->table",
			'description' => 'string',
		]);

		$section->title       = $request->title;
		$section->description = $request->description;
		$section->admin_name  = $request->admin_name;

		$section->save();

		return response()->json(['message' => 'section added successfully']);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$section = new Section();
		$data    = $section->findOrFail($id);

		return response()->json($data);
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
	 * @param  Section                      $section
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Section $section)
	{
        $request->validate([
            'title'       => "required|max:50",
            'color'       => "required",
            'description' => 'string',
        ]);

        $section->title = $request->title ;
        $section->color = $request->color ;
        $section->description  = $request->description;
        $section->save();
        return response()->json(['message' => 'section updated successfully' , 'data' => $section]);
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
