<?php

namespace App\Http\Controllers\API\User;

use App\Http\Resources\API\Activity\acrivityCollection;
use App\Http\Resources\API\User\TodoCollection;
use App\Models\Contract;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\User\UserCollection;
use App\Http\Resources\API\User\User as UserResource;
use App\Models\role;
use App\Models\Section;
use App\Models\User;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as clientRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler;
use Spatie\Activitylog\Models\Activity;
class UserController extends Controller
{
    protected $table;

    public function __construct()
    {
        $user        = new User();
        $this->table = $user->getTable();
        $this->middleware('scopes:create-user')->only('store');
        $this->middleware('scopes:update-user')->only('update');
        $this->middleware('scopes:delete-user')->only('destroy');
        $this->middleware('scopes:restore-user')->only('restore');
        $this->middleware('scopes:show-user-contracts')->only('user_contracts');
    }

    public function getCurrentUserRole()
    {
        $currentUser = request()->user('api')->withAccessToken(request()->header('Authorization'));

        return response()->json($currentUser->role);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $curUser = request()->user('api');
        $user    = new User();
        if ($curUser->role == 'admin') {
            return response()->json(['message' => __('scrum.api.get_success'), 'data' => $user->where('role', 'customer')->with(['contract'])->get()]);
        } else {
            return response()->json(['message' => __('scrum.api.get_success'), 'data' => $curUser]);
        }
//        return response()->json([request()->user('api')]);
        /* if ($authorized = request()->header('Authorization')) {
			 $currentUser = request()->user('api')->withAccessToken(auth());
			 }
 //        return $currentUser->role;
			 $data = $user->with(['contract'])->findOrFail($currentUser->id);
			 if ($data) {
				 return response()->json(['message' => 'success', 'user' => [$data]]);
			 }
		 }

		 return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);*/
    }

    public function user_contracts($id)
    {

        $contract = new Contract();
        $customer = new Customer();
        $customer = $customer::where('id',$id);

//        return $contract;

        $data     = $contract->where('old_id_customer', $customer->old_id_customer)->with(['type', 'customer', 'base_progress'])->get();

        if ($data) {
            return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
        }

        return response()->json(['message' => __('scrum.api.get_error'), 'data' => null], Response::HTTP_NO_CONTENT);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'     => "required|email|unique:$this->table",
            'password'  => 'required|min:6',
            'name'      => 'required',
         //   'role'      => 'required|in:admin,customer,staff',
//            'user_role' => 'required|integer'
        ]);

        $user            = new User();
        $user->name      = $request->name;
        $user->email     = $request->email;
        $user->username  = $request->userName ? : $request->email;
//        $user->user_role = $request->user_role;
        $user->role_id      = $request->role;
        $user->password  = Hash::make($request->password);
        $user->save();

        $success['name']  = $user->name;
        $success['token'] = $user->createToken('MyApp')->accessToken;

        $data = new UserResource($user);

        return response()->json(['message' => __('scrum.api.insert_success'), 'success' => $success , 'data' => $data], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user      = new User();
        $user_data = $user->findOrFail($id);

        if ($user_data) {
            return response()->json(['message' => __('scrum.api.get_success'), 'data' => $user_data], Response::HTTP_OK);
        }

        return response()->json(['message' => __('scrum.api.get_error'), 'data' => ['']], Response::HTTP_NO_CONTENT);
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
        $user = new User();
        $request->validate([
            'email'     => [
                'required',
                'unique:' . $this->table . ',email,' . $id,
                'max:250'
            ],
            'role' => 'integer',
            'name'      => 'string',
            'password'  => 'min:6'
        ]);

        $user::where('id', $id)->update([
            'role_id'    => $request->role,
            'email'     => $request->email,
            'name'      => $request->name,
            'password'  => Hash::make($request->password),
            'username'  => $request->userName ? : $request->email
        ]);
        return response()->json(['message' => 'user updated', 'data' => new UserResource($user->find($id))]);

//        return response()->json($user_data);
    }

    public function updateUserData(Request $request){

        $newData = $request->get('newData');
        $field = $request->get('dateField');
        $user = Auth::user();
        switch ($field) {
            case 'name':
                $request->validate([
                    'newData'      => 'string',
                ]);
                $user->name = $newData;
                $user->save();
                break;
            case 'userName':
                $user->username = $newData;
                $user->save();
                break;
            case 'email':
                $request->validate([
                    'newData'     => [
                        'required',
                        'email',
                        'unique:' . $this->table . ',email,' . $user->id,
                        'max:250'
                    ]
                ]);
                $user->email = $newData;
                $user->save();
                break;
            case 'mobile':
                $request->validate([
                    'newData'  => ['regex:/^(0|0098|\+98)9(0[1-5]|[1 3]\d|2[0-2]|98)\d{7}$/']
                ]);
                $user->phone_number = $newData;
                $user->save();
                break;
            case 'password':
                $request->validate([
                    'newData'  => 'min:6'
                ]);
                $user->password = Hash::make($newData);
                $user->save();
                break;
            default :
                break;
        }
        return response()->json(['message' => __('scrum.api.update_success', ['item' => __('scrum.string.status')]), 'data' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = new User();
        $user->find($id);
        $user::where('id', $id)->update([
            'active'   => 0
        ]);

        return response()->json(
            ['message' => 'User Role deleted']);//
    }

    public function restore($id) {
        $user = new User();
        $user->find($id);
        $user::where('id', $id)->update([
            'active'   => 1
        ]);
        return response()->json( [ 'message' => __( 'scrum.api.restore_success', [ 'item' => trans_choice( 'scrum.title.users', 1 ) ] ) ], Response::HTTP_OK );
    }

    public function listCustomers()
    {
        $user = new User();
        $data = $user->where('role', 'customer')->get(['id', 'name', 'email']);

        return response()->json($data);
    }

    public function listUsers() {
        if(Auth::user()->role == 'admin') {
        $users = User::orderBY('active' , 'desc')->get();
        }else{
            $users = User::where('active', 1)->orderBY('active' , 'desc')->get();
        }
        $json_users = new UserCollection($users);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $json_users]);
    }
    public function getActivity(){
        $activities = Activity::where('causer_id' , Auth::user()->id)->with('subject', 'causer')->paginate(15);
        $activities = new acrivityCollection($activities);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $activities], Response::HTTP_OK);
    }

    public function todoList(){
        $user = Auth::user() ;
        $note = $user->notifications;
        $notification = new TodoCollection($note);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $notification] , Response::HTTP_OK);
    }

    public function markAsRead($id) {
        $Notification = Auth::user()->Notifications->find($id);
        if($Notification && $Notification->read_at == null){
            $Notification->markAsRead();
        }
        return response()->json(['message' => __('scrum.api.insert_success')], Response::HTTP_OK);
    }
    public function getManager() {
        $managers = User::select('users.*')
            ->join("roles", "roles.id", "=", "users.role_id")
            ->where("roles.type" , 'manager')
            ->groupBy('users.id')
            ->get();
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => new UserCollection($managers)] , Response::HTTP_OK);
    }
    public function findManager($section_order) {
        if($section_order == 2 ){
            $section_order =  4 ;
        }
        $section = Section::where('order' , $section_order)->first();
        $role = role::where('section_id' , $section->id)->where('type' , 'manager')->first();

        return User::where('role_id' , $role->id)->first();
        
    }
}
