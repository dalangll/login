<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema;
use Illuminate\Support\Facades\Cache;
use Faker\Generator as FakerGenerator;
use Faker\Factory as FakerFactory;
use Validator;
use Illuminate\Support\Facades\Redis;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         /*在MySQL5.6下修复Laravel5.4默认字符串长度过长的问题*/
        Schema::defaultStringLength(191);
        /**
         * 手机号码格式验证
         */
        Validator::extend('phone', function ($arrtibute, $value, $parameters, $validator) {
            if (!is_numeric($value)) {
                return false;
            }
            return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,1,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
                $value);
        });
        /**
         * 短信验证码验证
         */
        
        Validator::extend('sms', function ($arrtibute, $value, $parameters, $validator) {
            /*判断验证码是否为数字，非数字即错误*/
            if (!is_numeric($value)) {
                return false;
            } else {
                /*从缓存中查找验证码*/
                $tmp = Redis::get('sms:'. $parameters[0]);
                /*判断验证码是否存在*/
                if ($tmp && $tmp == $value) {
                    /*验证成功*/
                    return true;
                }
                /*验证失败*/
                return false;
            }
        });

        /*邮箱验证*/

        Validator::extend('email', function ($arrtibute, $value, $parameters, $validator) {
            if (!is_numeric($value)) {
                return false;
            }
            return preg_match('[\w!#$%&\'*+/=?^_`{|}~-]+(?:\.[\w!#$%&\'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?',
                $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
