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
Route::get('/Post/test','v1\Test@test');
/////
Route::get('/Post/view','v1\Test@index');
Route::get('/Post/like','v1\Test@like');
Route::get('/Post/unlike','v1\Test@disLike');
Route::get('/Post/Search','v1\Test@Search');
Route::get('/Post/cats','v1\Test@cats');
Route::get('/Post/Get_comment','v1\Test@getComments');
Route::get('/Post/Add_comment','v1\Test@addComment');
Route::get('/Post/TopNewPosts','v1\Test@TopNewPosts');
Route::get('/Post/Main','v1\Test@MainPage');
/////user routing section
Route::get('/User/test','v1\User@test');
Route::get('/User/Register','v1\User@Register');
Route::get('/User/login','v1\User@Login');
Route::get('/User/details','v1\User@Details');
Route::get('/User/logout','v1\User@LogOut');
Route::get('/User/forgotpw','v1\User@ForgetPassword');
Route::get('/User/forgotpwprocess','v1\User@ForgetPasswordProcess');
Route::get('/User/updateUser','v1\User@editProfile');
Route::get('/User/changePass','v1\User@changePassword');
Route::get('/Other/reportBug','v1\Other@sendBug');
////email section
Route::get('/Email','v1\User@emailVerify');
///how the fk should i add parameters to it :|
//
