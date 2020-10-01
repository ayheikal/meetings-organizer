<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;
use \App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
Route::group(['prefix'=>'v1'],function (){
    Route::apiResource('meeting/registration',RegistrationController::class);
    Route::apiResource('meeting',MeetingController::class);
    Route::post('user/signin',[AuthController::class,'signin']);
    Route::post('user/register',[AuthController::class,'store']);

    Route::post('user/logout',[AuthController::class,'logout']);


});

