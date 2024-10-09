<?php

namespace App\Http\Controllers\API\Contract;

use App\Models\ContractType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContractTypeController extends Controller {

    public function __construct() {
        $this->middleware('scopes:create-contractType')->only('store');
        $this->middleware('scopes:update-contractType')->only('update');
        $this->middleware('scopes:delete-contractType')->only('destroy');
        $this->middleware('scopes:restore-contractType')->only('restore');
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$ct = new ContractType();

		return response()->json( $ct->get( [ 'id', 'title', 'description', 'deleted_at' ] ) );

	}

	public function showAllWithTrashed() {
		$ct   = new ContractType();
		$data = $ct::withTrashed()->get( [ 'id', 'title', 'description', 'deleted_at' ] );
		foreach ( $data as $item ) {
			$item->trashed = $item->trashed();
		}

		return response()->json( $data );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		$ct = new ContractType();

		$request->validate( [
			'title'       => 'required|unique:' . $ct->getTable(),
			'description' => 'string'
		] );

		$ct->title       = $request->title;
		$ct->description = $request->description ?: '';
		$ct->save();

		return response()->json( [ 'message' => 'contract type created', 'data' => $ct->title ] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( $id ) {
		$ct   = new ContractType();
		$data = $ct::withTrashed()->find( $id );
		if ( $data ) {
			return response()->json( [
					'message' => __( 'scrum.api.get_success' ),
					'data'    => [
						'title'       => $data->title,
						'description' => $data->description,
						'trashed'     => $data->isDeleted(),
					]
				], 200 );
		}

		return response()->json( [ 'message' => __( 'scrum.api.not_found' ) ], Response::HTTP_NOT_FOUND );

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, $id ) {
		$ct = new ContractType();
		$request->validate( [
			'title'       => 'required|unique:' . $ct->getTable() . ',title,' . $id,
			'description' => 'string',
		] );
		$ct->title       = $request->title;
		$ct->description = $request->description ?: $ct->description;

		$ct::where( 'id', $id )->update( [
			'title'       => $ct->title,
			'description' => $ct->description
		] )
		;

		return response()->json( [ 'message' => 'Contract Type updated', 'data' => $ct ], Response::HTTP_OK );
	}

	public function restore( $id ) {
		$ct = new ContractType();
		$ct::withTrashed()->findOrFail( $id );
		$ct::where( 'id', $id )->restore();

		return response()->json( [ 'message' => 'restored successfully' ], Response::HTTP_OK );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( $id ) {
		$ct = new ContractType();
		$ct->findOrFail( $id );
		$ct::where( 'id', $id )->delete();

		return response()->json( [ 'message' => 'contract type deleted successfully' ], Response::HTTP_OK );
	}
}
