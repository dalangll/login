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

  $api->group(['middleware' => 'AesMiddleware'],function ($api){
      $api->post('sendinfo','AppController@send');//发送短信
  });

  $api->get('getip','AppController@getIps');
    $api->get('getaddress','AppController@aoliaddress');
    $api->get('getcookie','AppController@getcookie');
    /*用户上次登录时间*/
    $api->get('getlasttime','UserController@getLoginTime');
    $api->get('testredis','AppController@testip');




   /*邮件*/
$api->get('lock/{id}','UserController@lock');
    $api->post('sendmail','AuthController@testMail');
    $api->any('getmail','AuthController@activateMail');
    $api->any('gel','AuthController@act');

    $api->get('getaddre','AppController@getDistance');

    $api->get('getaec','CategoryController@postAec');
    $api->get('/',function (){
        return view('login');
    });

    /*pay*/
   $api->any('pay','PayController@pay');
   $api->any('verfly','PayController@verfly');
   $api->any('notifly','PayController@notifly');

    $api->get('path','TestController@testpath');
    $api->get('model','PayController@model');
});

