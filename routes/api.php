<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

////TEST
Route::get('/post/test','Test@test');
/////
Route::get('/post/view','Test@index');
Route::get('/post/like','Test@like');
Route::get('/post/unlike','Test@disLike');
Route::get('/post/Search','Test@Search');
Route::get('/post/cats','Test@cats');
Route::get('/post/Get_comment','Test@getComments');
Route::get('/post/Add_comment','Test@addComment');
Route::get('/post/TopNewPosts','Test@TopNewPosts');
Route::get('/post/Main','Test@MainPage');
/////user routing section
Route::get('/user/test','User@test');
Route::get('/user/Register','User@Register');
Route::get('/user/login','User@Login');
Route::get('/user/details','User@Details');
Route::get('/user/logout','User@LogOut');
Route::get('/user/forgotpw','User@ForgetPassword');
Route::get('/user/forgotpwprocess','User@ForgetPasswordProcess');
////email section
Route::get('/email','User@emailVerify');
///how the fk should i add parameters to it :|
//
