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
$api = app('Dingo\Api\Routing\Router');

$api->version('v1',['namespace'=>'App\Http\Controllers\V1\Frontend'],function($api){
 // 用户认证
  $api->post('register', 'AuthController@register');
  $api->post('login', 'AuthController@login');
  $api->get('test', function(){
  	return 'ok';
  });
  $api->post('sendsms','AppController@send');
  $api->get('getsms','AuthController@getsms');
});

