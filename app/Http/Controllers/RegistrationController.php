<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
class RegistrationController extends Controller
{
    public function __construct(){
        $this->middleware('jwt.auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "its working";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        error_log("#######################################");
        $request->validate([
            'meeting_id'=>'required'
        ]);
        if(!$user=JWTAuth::parseToken()->authenticate())
        {
            return response()->json(['msg'=>"not authorized"],501);
        }
        $meeting_id=$request->meeting_id;
        $meeting=Meeting::findOrFail($meeting_id);
        $response=[
            'msg'=>"U already registered before",
            'user'=>$user,
            'meeting'=>$meeting
        ];
        if($meeting->users()->where("user_id",$user->id)->first())
        {
            return response()->json($response,404);
        }
        if($user->meetings()->attach($meeting)) {
            return response()->json(["msg" => "successfully registered"], 200);
        }
        else {
            return response()->json(["msg" => "failed to register"], 404);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting=Meeting::findOrFail($id);
        if(!$user=JWTAuth::parseToken()->authenticate())
        {
            return response()->json(['msg'=>'user not authorized'],501);
        }
        if(!$meeting->users()->where('user_id',$user->id)->first())
        {
            return response()->json(['msg'=>'user not registered for this meeting, delete operation not correct'],501);
        }
        $meeting->users()->detach($user);

        return response()->json(['msg'=>'delted successfuly'],200);
    }
}
