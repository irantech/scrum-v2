<?php

namespace App\Http\Controllers\API\Contract;

use App\Models\Ancillary;
use App\Models\AncillaryProgress;
use App\Models\AncillarySubProgress;
use App\Http\Controllers\Controller;
use App\Models\SubProgress;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AncillaryController extends Controller {


    public function __construct()
    {
        $this->middleware('scopes:create-ancillary')->only('store');
        $this->middleware('scopes:update-ancillary')->only('update');
        $this->middleware('scopes:delete-ancillary')->only('destroy');
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$ancillary = new Ancillary();
		$data      = $ancillary::orderBy( 'id', 'DESC' )->with( [
			'base_progress:title,description',
			'sub_progress',
			//            'contract'
		] )->get( [ 'id', 'contract_id', 'contract_code', 'title' ] )
		;

		return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data ] );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		$request->validate( [
			'title'         => 'required|string',
			'contract_code' => 'required',
			'contract_id'   => 'required|exists:App\Models\Contract,id'
		] );

		$ancillary                = new Ancillary();
		$ancillary->title         = $request->get( 'title' );
		$ancillary->contract_code = $request->get( 'contract_code' );
		$ancillary->contract_id   = $request->get( 'contract_id' );
		$ancillary->save();

		return response()->json( [ 'success' => true, 'message' => __( 'scrum.api.insert_success', [ 'item' => trans_choice( 'scrum.title.contracts', 1 ) ] ) , 'data' => $ancillary ], 200 );

	}

	/**
	 * Display the specified resource.
	 *
	 * @param Ancillary $ancillary
	 *
	 * @return \Illuminate\Http\Response
	 * @internal param int $id
	 *
	 */
	public function show( Ancillary $ancillary ) {
		$data = $ancillary::where( 'id', $ancillary['id'] )->with([
			'base_progress',
			'sub_progress',
			'contract:id,title'
		])->first();

		return response()->json( [ 'message' => __( 'scrum.api.get_success' ), 'data' => $data ] );

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param Ancillary $ancillary
	 *
	 * @return \Illuminate\Http\Response
	 * @throws \Exception
	 */

	public function updateTitle( Request $request, $id ) {

		$ancillary = Ancillary::findOrFail($id);
		$request->validate( [
			'title' => 'required'
		] );
		$ancillary->update([
			'title' => $request->title,
		]);

		return response()->json( [
			'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.contracts', 1 ) ] ),
			'data'    => $ancillary
		] );

		//		return response()->json( [ 'message' => 'scrum.api.update_success', 'success' => true ] );

	}

	public function update( Request $request, Ancillary $ancillary ) {
		//		$a = new Ancillary();
		//		$contract = $a->find($ancillary);
		//        $ancillarys = [];
		DB::beginTransaction();
		//		$ap = new AncillaryProgress();
		//		$asp = new AncillarySubProgress();

		$ancillary->base_progress()->detach();
		$ancillary->sub_progress()->detach();
		foreach ( $request->softwares as $software ) {
			$allCp = \App\Models\BaseProgress::where( 'software_id', $software )->get();
			foreach ( $allCp as $progress ) {
				$ancillary->base_progress()->attach( $progress['id'] );
				$sub = \App\Models\SubProgress::where( 'id', $progress['id'] )->get();
				//                return $sub;
				foreach ( $sub as $item ) {
					$ancillary->sub_progress()->attach( $item['id'] );
				}
			}
		}
		DB::commit();
		return response()->json( [
			'message' => __( 'scrum.api.update_success', [ 'item' => trans_choice( 'scrum.title.contracts', 1 ) ] ),
			'data'    => $this->show( $ancillary )->original['data']
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
		$ancillary = new Ancillary();
		$data      = $ancillary->findOrFail( $id );
		$data->delete();

		return response()->json( [ 'message' => __( 'scrum.api.remove_success', [ 'item' => trans_choice( 'scrum.title.contracts', 1 ) ] ) ] );
	}


	public function ChangeSubProgressStatus( Request $request ) {

		/* $request->validate([
			 'sub_progress_id' => 'required|int',
			 'contract_id'     => 'required|int',
			 'status'          => 'required|in:hold,complete,running,cancel'
		 ]);*/

		$sp_id  = $request->sub_progress_id;
		$status = $request->status;
		$aid    = $request->ancillary_id;


		$pivot = new AncillarySubProgress();
		$data  = $pivot->where( 'ancillary_id', $aid )->where( 'sub_progress_id', $sp_id )->latest( 'id' )->first();

		$data->status = $status;
		$data->save();

		//        $contract = new Contract();
		//        $data     = $contract->findOrFail($cid);
		//        $data = $contract->sub_progress()->attach(
		//            [
		//                $cid=>
		//                [
		//                    'status'          => $status,
		//                    'sub_progress_id' => $sp_id,
		//                ]
		//            ]
		//        );

		return response()->json( [ 'message' => __( 'scrum.api.update_success', [ 'item' => __( 'scrum.string.status' ) ] ), 'data' => $data ] );

	}

	public function ChangeBaseProgressStatus( Request $request ) {
		$bp_id  = $request->base_progress_id;
		$status = $request->status;
		$cid    = $request->ancillary_id;
		$pivot  = new AncillaryProgress();
		//        return $request->all();
		$data = $pivot->where( 'ancillary_id', $cid )->where( 'base_progress_id', $bp_id )->latest( 'id' )->first();

		$data->status = $status;
		$data->save();

		return response()->json( [ 'message' => __( 'scrum.api.update_success', [ 'item' => __( 'scrum.string.status' ) ] ), 'data' => $data ] );

	}

}
