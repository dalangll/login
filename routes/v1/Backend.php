<?php

/*获取Dingo API服务容器*/
$api = app(\Dingo\Api\Routing\Router::class);

/*设置API的版本为v1*/
$api->version('v1', ['namespace' => 'App\Http\Controllers\V1\Backend'], function($api) {

    $api->group(['prefix'=>'admin'],function ($api){
    /*商品分类*/
    $api->group(['prefix'=>'category','middleware'=>'api.throttle','limit' => 5, 'expires' => 1],function ($api){

        $api->post('create','CategoryController@create');//创建分类
        $api->post('update/{id}','CategoryController@update');//修改分类
        $api->get('show','CategoryController@show');//分类列表

    });
    /*商品*/
    $api->group(['prefix'=>'goods'],function ($api){

        $api->post('create','GoodsController@add');//添加商品
        $api->post('update/{id}','GoodsController@update');//更新修改商品
        $api->get('show','GoodsController@listgoods');//商品列表
        $api->delete('delete/{id}','GoodsController@delete');//删除商品
        $api->get('copy/{id}','GoodsController@copygood');//复制新建商品
        $api->post('reset/{id}','GoodsController@resetPrice');//重置商品价格


    });


    });

});



