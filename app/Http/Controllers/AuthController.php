<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $hasher = app()->make('hash');

        if (empty($email)) {
            return response()->json([
                'status' => "failed",
                'message'=> 'Email required!',
                'data'   => (object)[]
            ],200);
        }

        if (empty($password)) {
            return response()->json([
                'status' => "failed",
                'message'=>'Password required!',
                'data'   => (object)[]
            ],200);
        }

        $user = User::where('email', '=', $email)->first();

        if (empty($user)) {
            return response()->json([
                'status' => "failed",
                'message'=>'Your email is incorrect!',
                'data'   => (object)[]
            ],200);
        }

        $check_password = $hasher->check($password, $user['password']);
        $token = sha1(time());

        $update_token = User::where('email', '=', $email)->update([
            'token' => $token,
        ]);

        if ($check_password && $update_token) {
            $userNew = User::where('email', '=', $email)->first();
            return response()->json([
                'status' => "success",
                'message'=>'Your email is incorrect!',
                'token'   => $token,
                'data'   => $userNew
            ],200);
        } else {
            return response()->json([
                'status'  => "success",
                'message' =>'Your password is incorrect!',
                'token'   => "",
                'data'    =>(object)[]
            ],200);
        }
    }

    public function register(Request $request)
    {

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if(empty($name)){
            return response()->json([
                'status' => "failed",
                'message'=> 'Name required!',
                'data'   => (object)[]
            ],200);
        }

        if(empty($email)){
            return response()->json([
                'status' => "failed",
                'message'=> 'Email required!',
                'data'   => (object)[]
            ],200);
        }

        if(empty($password)){
            return response()->json([
                'status' => "failed",
                'message' => 'Password required!',
                'data'   => (object)[]
            ],200);
        }


        $user = User::where('email', '=', $email)->first();

        if (!empty($user)) {
            return response()->json([
                'status' => "failed",
                'message' => 'Email already used!',
                'data'   => (object)[]
            ],200);
        }

        $token = sha1(time());

        $add_user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'token'    => $token,
        ]);

        if ($add_user) {
            return response()->json([
                'status' => "success",
                'data'   => $add_user
            ],200);
        } else {
            return response()->json([
                'status' => "failed",
                'data'   => (object)[]
            ],200);
        }
       
    }

    public function check_user(Request $request)
    {
        $email = $request->input('email');
        if(empty($email)){
            return response()->json([
                'status' => "failed",
                'message' => "Email required!",
                'data'   => (object)[]
            ],200);
        }

        $user = User::where('email','=',$email)->first();

        if($user)
        {
            return response()->json([
                'status' => "success",
                'data'   => (object)[]
            ],200);
        } else {
            return response()->json([
                'status' => "failed",
                'data'   => (object)[]
            ],200);
        }
    }


    public function get_profile(Request $request)
    {
        return response()->json([
            'status' => "success",
            'data'   => $request->get('auth')
        ],200);
    }

}
