<?php

/*获取Dingo API服务容器*/
$api = app(\Dingo\Api\Routing\Router::class);

/*设置API的版本为v1*/
$api->version('v1', ['namespace' => 'App\Http\Controllers\V1\Backend'], function($api) {

    $api->group(['prefix'=>'admin'],function ($api){
    /*商品分类*/
    $api->group(['prefix'=>'category','middleware'=>'AesMiddleware'],function ($api){
        $api->post('create','CategoryController@create');
        $api->get('getidd','CategoryController@getidd');
        $api->post('update/{id}','CategoryController@update');
        $api->get('show','CategoryController@show');


    });
    /*商品*/
    $api->group(['prefix'=>'goods'],function ($api){
        $api->post('create','GoodsController@add');
        $api->post('update/{id}','GoodsController@update');
        $api->get('show','GoodsController@listgoods');
        $api->delete('delete/{id}','GoodsController@delete');
        $api->get('copy/{id}','GoodsController@copygood');
        $api->post('reset/{id}','GoodsController@resetPrice');


    });

    });

});



