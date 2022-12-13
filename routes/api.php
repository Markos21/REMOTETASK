<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'middleware' => 'api',
    'namespace' =>'App\Http\Controllers',

], function () {
    Route::post('store', 'FileHandlerController@store'); 
    Route::get('getData', 'FileHandlerController@get'); 
    Route::get('show/{id}', 'FileHandlerController@show'); 

    // helps to download temp files
    Route::get('local/temp/{path}', function (string $path){
        return Storage::disk('local')->download($path);})->name('local.temp');
    });
