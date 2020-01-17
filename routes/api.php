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

Route::group(['as'=>'api.'], function () {
    Route::post('token', 'Api\DetectController@token')->name('token');

    Route::group(['middleware'=>['force.json','frontend']], function () {
        Route::post('domain', 'Api\DetectController@domain')->name('domain');
        Route::post('detect_result', 'Api\DetectController@detectResult')->name('detect_result');
        
        Route::post('jump_urls', 'Api\JumpController@urls')->name('jump_urls');
        Route::post('jump_logs', 'Api\JumpController@logs')->name('jump_logs');
    });
});
