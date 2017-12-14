<?php
namespace Hongli\Alipay;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    public function boot(){

    }

    public function register(){
        /*合并配置文件*/
        $this->mergeConfigFrom(
            __DIR__ . '/Config/config.php', 'alipay'

        );
        /*绑定*/
        $this->app->bind('alipay.base', function ($app) {
            $aliLog = new Alipay();
            return $aliLog;
        });
    }
}
