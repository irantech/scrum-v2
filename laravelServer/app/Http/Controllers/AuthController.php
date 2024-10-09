<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $input     = $request->all();
        $validator = Validator::make($input, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('login');
//            return view('auth.login',['error'=>$validator->errors()]);
        }
        $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {
            $user             = Auth::user();
            $success['token'] = $user->createToken('it_scrum', ['*'])->accessToken;

            return redirect('/');
//            return response()->json(['success' => $success], Response::HTTP_OK);
        } else {
            return redirect('login');
//            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
