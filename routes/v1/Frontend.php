<?php

/*获取Dingo API服务容器*/
$api = app(\Dingo\Api\Routing\Router::class);

/*设置API的版本为v1*/
$api->version('v1', ['namespace' => 'App\Http\Controllers\V1\Frontend'], function($api) {
    $api->group(['prefix'=>'goods'],function ($api){
        $api->get('showcategory','GoodsController@showcategory');
        $api->get('categorygoods/{id}','GoodsController@categorygoods');
        $api->get('showgoods','GoodsController@showGoods');
        $api->get('particulars/{id}','GoodsController@particulars');
        $api->get('tuenParticulars/{id}','GoodsController@tuenParticulars');
    });
});