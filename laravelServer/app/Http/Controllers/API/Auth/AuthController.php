<?php
namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\API\Auth\user as ResourceUser;

class AuthController extends Controller
{

    public function __construct() {
//        $this->middleware('scopes:force-login')->only('force_login');
    }

    public $successStatus = Response::HTTP_OK;
    public $unauthorizedStatus = Response::HTTP_UNAUTHORIZED;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password]) ||
            Auth::attempt(['username' => $request->email, 'password' => $request->password])
        ) {
            $user = Auth::user();

            $request->request->add(['scope'=>$user->role]);
            // forward the request to the oauth token request endpoint
            $tokenRequest = Request::create(
                '/oauth/token',
                'post'
            );

            Route::dispatch($tokenRequest);

            $role = $user->getRole();

            $permissions = $role->permissions->pluck('title')->toArray();
            $success['token'] = $user->createToken('IranTech',$permissions)->accessToken;
            activity()
                ->causedBy($user)
                ->log('user_logged_in');

            return response()->json(['success' => $success,'role'=>$permissions], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised' , 'message' => 'unauthorised'], $this->unauthorizedStatus);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'email'      => 'required|email',
            'password'   => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }

        $input             = $request->all();

        $input['password'] = Hash::make($input['password']);
        $user              = User::create($input);
        $success['token']  = $user->createToken('IranTech',[$user->role])->accessToken;
        $success['name']   = $user->name;

        return response()->json(['success' => $success], $this->successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();

//        return $tokenRequest;
        return response()->json(['data' => new ResourceUser($user)], $this->successStatus);
    }

	public function logout(Request $request)
	{
        $user = Auth::user();
		$request->user()->token()->revoke();
        activity()
            ->causedBy($user)
            ->log('user_logged_out');
		return \response()->json(['success'=>true]);
    }

    public function force_login(User $user)
    {
        Auth::login($user);
        Auth::user();

        // forward the request to the oauth token request endpoint
        $tokenRequest = Request::create(
            '/oauth/token',
            'post'
        );

        Route::dispatch($tokenRequest);

        $role = $user->getRole();

        $permissions = $role->permissions->pluck('title')->toArray();
        $success['token'] = $user->createToken('IranTech',$permissions)->accessToken;
        activity()
            ->causedBy($user)
            ->log('user_force_logged_in');

        return response()->json(['success' => $success,'role'=>$permissions], $this->successStatus);
    }
}
