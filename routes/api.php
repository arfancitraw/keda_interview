<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('delete', 'AuthController@delete');
    Route::post('me', 'AuthController@me');
    Route::get('customerList', 'AuthController@getCustomerList');
});

Route::group(['prefix' => 'message'], function () {
    Route::post('send', 'MessageController@send');
    Route::get('myMessage', 'MessageController@getMyMessage');
    Route::get('allMessage', 'MessageController@getAllMessage');
});

Route::group(['prefix' => 'report'], function () {
    Route::post('send', 'ReportController@send');
    Route::get('allReport', 'ReportController@getAllReport');
});