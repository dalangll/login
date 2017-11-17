<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mrgoon\AliSms\AliSms;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
class AppController extends BaseController
{
    /*发送短信验证码*/
      public function send(Request $request){
          $mobile =  $request->get('mobile');
          $code = rand(1000,9999);
          $sms = app(AliSms::class);
          $sms->sendSms($mobile,'SMS_92580003', ['number' => $code]);
          /*验证码存入redis，过期时间为5分钟*/
          Redis::set('sms',$code,'EX',300);
          return $code;

      }
}
