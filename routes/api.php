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
Route::get('/post/cats','Test@cats');
Route::get('/post/Get_comment','Test@getComments');
Route::get('/post/Add_comment','Test@addComment');
///how the fk should i add parameters to it :|
//
