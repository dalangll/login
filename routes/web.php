<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return 132;
    return view('welcome');
});
Route::get('asd','app\Http\v1\AuthController@act');


Route::get('/submit','app\Http\Controller\v1\Frontend\AppController@testlogin');