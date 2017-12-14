<?php

/*获取Dingo API服务容器*/
$api = app(\Dingo\Api\Routing\Router::class);

/*设置API的版本为v1*/
$api->version('v1', ['namespace' => 'App\Http\Controllers\V1\Frontend'], function($api) {

    /*用户认证*/
    $api->group(['prefix'=>'member'],function($api){
        $api->post('register', 'AuthController@register');//注册
        $api->post('login', 'AuthController@login');//登录
        $api->get('logout', 'AuthController@destroy');//退出登录
        $api->get('newtoken','AppController@refreshToken');//刷新token

    });
    /*商品*/
    $api->group(['prefix'=>'goods'],function ($api){
        $api->get('showcategory','CategoryController@showcategory');//所有分类
        $api->get('categorygoods/{id}','GoodsController@categorygoods');//某个分类下的所有商品
        $api->get('showgoods','GoodsController@showGoods');//所有商品
        $api->get('particulars/{id}','GoodsController@particulars');//商品详情
        $api->get('tuenParticulars/{id}','GoodsController@tuenParticulars');//商品图文详情
    });
    /*系统信息*/
    $api->group(['prefix'=>'app'],function ($api){
        $api->post('sendinfo','AppController@send');//发送短信
    });
    /*支付*/
    $api->group(['prefix'=>'pay'],function ($api){
        $api->get('getpayf','AlipayController@alipayf');
    });


});