<?php

namespace App\Http\Controllers\API\Progress;

use App\Models\BaseProgress;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseProgressController extends Controller {
	use SoftDeletes;
	protected $table;

	public function __construct() {
		$base_progress = new BaseProgress();
		$this->table   = $base_progress->getTable();

        $this->middleware('scopes:create-baseProgress')->only('store');
        $this->middleware('scopes:update-baseProgress')->only('update');
        $this->middleware('scopes:delete-baseProgress')->only('destroy');
        $this->middleware('scopes:restore-baseProgress')->only('restore');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$base_progress = new BaseProgress();
		$data          = $base_progress::idDescending()->with( [ 'progress:id,base_progress_id', 'contract' ] )->get( [
			'id',
			'section_id',
			'software_id',
			'user_role',
			'title',
			'description',
			'percentage',
			'private_description'
		] )
		;
		if ( $data ) {
			return response()->json( $data, Response::HTTP_OK );
		}

		return response()->json( [ 'message' => __( 'scrum.api.no_data' ) ], Response::HTTP_NO_CONTENT );

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		$base_progress = new BaseProgress();
		$request->validate( [
			'section_id'          => 'required|integer',
			'software_id'         => 'required|integer',
			'user_role'           => 'string|in:admin,office,support,accountant,programmer,graphic',
			'title'               => 'required|unique:' . $this->table,
			'description'         => 'string',
			'private_description' => 'string',
			'percentage'          => 'integer'
		] );


		$base_progress->section_id          = $request->section_id;
		$base_progress->software_id         = $request->software_id;
		$base_progress->user_role           = $request->user_role;
		$base_progress->title               = $request->title;
		$base_progress->description         = $request->description;
		$base_progress->private_description = $request->private_description;
		$base_progress->percentage          = $request->percentage;
		$base_progress->save();

		return response()->json( [
			'message' => __( 'scrum.api.insert_success' ),
			'data'    => [ 'details' => $base_progress ]
		], Response::HTTP_CREATED );
	}


	public function showAllWithTrashed() {
		$bp   = new  BaseProgress();
		$data = $bp::withTrashed()->orderBy( 'id', 'DESC' )->with( [ 'software:id,title', 'progress'] )->get( [
			'id',
			'title',
			'software_id',
			'user_role',
			'section_id',
			'description',
			'percentage',
			'private_description',
			'deleted_at'
		] )
		;
		foreach ( $data as $item ) {
			$item->trashed = $item->trashed();
		}

		return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data ], Response::HTTP_OK );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( $id ) {
		$base_progress = new BaseProgress();
		$data          = $base_progress::with( [ 'progress:id,base_progress_id,section_id,description,title,deleted_at' ] )->find( $id );

		foreach ( $data->progress as $item ) {
			//            return $item->trashed();
			$item->trashed = $item->trashed();
		}
		if ( $data ) {
			return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data ], Response::HTTP_OK );
		}

		return response()->json( [
			'message' => __( 'scrum.api.not_found' ),
			'title'   => trans_choice( 'scrum.title.base_progress', 1 )
		], Response::HTTP_NOT_FOUND, [], JSON_UNESCAPED_UNICODE );
	}


	public function update( Request $request, $id ) {
		$bp = new BaseProgress();
		$request->validate( [
			'section_id'          => 'required|integer',
			'software_id'         => 'required|integer',
			'user_role'           => 'string|in:admin,support,office,accountant,support,programmer,graphic',
			'title'               => 'required|unique:' . $bp->getTable() . ',id,' . $id,
			'description'         => 'string|unique:' . $bp->getTable() . ',id,' . $id,
			'private_description' => 'string',
			'percentage'          => 'integer'
		] );
		$getBp                      = $bp::where( 'id', $id )->first();
		$getBp->section_id          = $request->section_id;
		$getBp->software_id         = $request->software_id;
		$getBp->user_role           = $request->user_role;
		$getBp->title               = $request->title;
		$getBp->description         = $request->description;
		$getBp->private_description = $request->private_description;
		$getBp->percentage          = $request->percentage;
		$getBp->save();

		/*    $bp::where('id', $id)->update([
				'section_id'          => $bp->section_id,
				'software_id'         => $bp->software_id,
				'title'               => $bp->title,
				'description'         => $bp->description,
				'private_description' => $bp->private_description,
				'percentage'          => $bp->percentage
			]);*/

		return response()->json( [
			'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.base_progress', 1 ) ] ),
			'data'    => $getBp
		] );

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( $id ) {
		$bp= BaseProgress::findOrFail( $id );
        $bp->delete();

		return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.base_progress', 1 ) ] ) ] );
	}


	public function restore( $id ) {
		$bp = BaseProgress::withTrashed()->findOrFail($id);
        $bp->restore();

		return response()->json( [ 'message' => __( 'scrum.api.restore_success', [ 'item' => trans_choice( 'scrum.title.base_progress', 1 ) ] ) ], Response::HTTP_OK );
	}
}
