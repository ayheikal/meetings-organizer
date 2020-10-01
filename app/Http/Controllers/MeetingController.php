<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use JWTAuth;
class MeetingController extends Controller
{
    public function __construct(){
        $this->middleware('jwt.auth')->only('update','store','destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings=Meeting::with('users')->get();

        return response()->json($meetings);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $this->validate($request,[
           'title'=>'required|min:4',
           'time'=>'required:date_format:YmdHie',
           'description'=>'required'
       ]);
       if(!$user=JWTAuth::parseToken()->authenticate()){
           return response()->json(['msg'=>'user not found'],404);
       }
       $meeting=Meeting::create([
           'title'=>$request->title,
           'time'=>Carbon::createFromFormat("YmdHie",$request->time),
           'description'=>$request->description,
       ]);
       if($meeting->save()){
           $meeting->users()->attach($user->id);

           return response()->json(["message"=>"meeting created"],200);
       }
        return response()->json(["message"=>"meeting dosenot created"],404);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting=Meeting::with('users')->findOrFail($id);
        $response=[
            'msg'=>'meeting info',
            'meeting'=>$meeting,
        ];
        return  response()->json($response,200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title'=>'required|min:4',
            'time'=>'required:date_format:YmdHie',
            'description'=>'required',

        ]);
        if(!$user=JWTAuth::parseToken()->authenticate()){
            return response()->json(['msg'=>'user not found'],404);
        }
        $meeting=Meeting::with('users')->findOrFail($id);
//        var_dump($meeting);
        if (!$meeting->users()->where("user_id",$user->id)->first())
        {
            return response()->json(["msg"=>"user not register for this meeting"],404);
        }
        $meeting->time=Carbon::createFromFormat("YmdHie",$request->time);
        $meeting->title=$request->title;
        $meeting->description=$request->description;


        if(!$meeting->update())
        {
            return response()->json(["msg"=>"meeting Not updated"],404);
        }
        $response=[
            "msg"=>"Meeting updated",
            "meeting"=>$meeting
        ];
        return response()->json($response,200);


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
        if(!$user=JWTAuth::parseToken()->authenticate()){
            return response()->json(['msg'=>'user not authorized'],404);
        }
        if (!$meeting->users()->where("user_id",$user->id)->first())
        {
            return response()->json(["msg"=>"user not register for this meeting"],404);
        }
        $users=$meeting->users()->detach();
        if(!$meeting->delete())
        {
            foreach ($users as $user){
                $meeting->users()->attach($user);
            }return response()->json(["message"=>"Meeting Not Deleted"],404);

        }
        return response()->json(["message"=>"Meeting Deleted"],200);




    }
}
