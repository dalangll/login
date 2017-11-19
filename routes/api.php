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
  $api->post('register', 'AuthController@register');//注册
  $api->post('login', 'AuthController@login');//登录
  $api->get('logout', 'AuthController@destroy');//退出登录
  $api->get('newtoken','AppController@refreshToken');//刷新token

  
  $api->post('sendsms','AppController@send');//发送短信
  $api->get('getsms','AuthController@getsms');
    $api->get('getcookie','AppController@getcookie');
    $api->get('setcookie','AppController@cookie');
    $api->get('testyy',function(){
        $arr = [
            'a'=>1,
            'b'=>2,
            'c'=>3
        ];

        return $arr['a'];
    });

    /*商品分类管理*/
   $api->group(['prefix' => 'sso', 'middleware' => 'SsoMiddleware'],function($api){

       $api->get('testy',function(){
          return 123456;
       });
   });



});

