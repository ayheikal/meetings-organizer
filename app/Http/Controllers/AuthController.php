<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth')->only('logout');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $credientials=$request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:4'

        ]);
       $user= User::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=>bcrypt($request->password)
        ])->save();
       if($user){
//           $user->signin=[
//               'href'=>'api/v1/api/signin',
//               'method'=>'POST',
//               'params'=>'email, password'
//           ];
           return response()->json($user,201);
       }
        return response()->json(['message'=>'Not created'],404);


    }



    public function signin(Request $request )
    {
        $request->validate([
            'email'=>'required|exists:users,email',
            'password'=>'required'
        ]);
        $credentials=$request->only('email','password');
    try {
        if (!$token = JWTAuth::attempt($credentials))
        {
            return response()->json(['msg'=>"Invalid Credentials"],401);

        }
    }catch (JWTException $e)
    {
        return response()->json(['msg'=>"Could not craete a token"],500);
    }
        return response()->json(['token'=>$token],200);

    }

    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['msg'=>"successfully loged out"],200);
    }

}
