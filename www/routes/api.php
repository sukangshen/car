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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});



Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');

    //登录
    Route::post('logout', 'UserController@logout');
    Route::post('me', 'UserController@me');
    Route::post('refresh', 'UserController@refresh');

    //微信
    Route::any('auth', 'UserController@auth');
    Route::any('callback', 'UserController@callback');


});

Route::post('/upload', 'Api\UploadController@index');



