<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mrgoon\AliSms\AliSms;
use Illuminate\Support\Facades\Redis;
use Crypt;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use Response;
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
      /*刷新token*/
      public function refreshToken(){
      	/*获取refresh_token*/
      	 $member=JWTAuth::user();
      	 return $member->id;
         $refreshToken = $this->getrefreshtoken($member->id);
   
        /*解密refresh_token*/
        $decrypted = Crypt::decrypt($refreshToken);
        $id = $decrypted['id'];
        $refresh_ttl = $decrypted['refresh_ttl'];
        /*获取当前用户token*/
        $token = JWTAuth::getToken();
        /*解析出用户id*/
        $parseToken = json_decode(base64_decode(explode('.', $token)[1]), true);
         
        /*检验token与refresh_token是否同一用户*/
        if($id==$parseToken['sub'] && strtotime($refresh_ttl) > time()){
           $newtoken = JWTAuth::refresh($token);
        }else{
        	 abort(500, 'token刷新失败，请稍后重试');
        }
        
        $result =
          [
          'data'=>[
               'id'=>$parseToken['sub'],
               'token_type'=>'Bearer',
               'token'=>$newtoken,
               'refresh_token' => $refreshToken,
                'expired_at' => Carbon::now()->addMinutes(config('jwt.ttl'))->toDateTimeString(),
                'refresh_expired_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString()

          ]

        ];

        return response($result,201);

        
      }
      public function cookie(Request $request){
          //setcookie("login","",time()-1);
          Redis::delete('8');
      }
      public function getcookie(){
         $cookieToken=$_COOKIE['login'];
         if($cookieToken){
             $parseToken = json_decode(base64_decode(explode('.', $cookieToken)[1]), true);
             $redisToken = Redis::get($parseToken['sub']);
         }

    }

    public function handle($request, Closure $next)
    {
        $userInfo = \Session::get('user_login');
        if ($userInfo) {
            // 获取 Cookie 中的 token
            $singletoken = $request->cookie('SINGLETOKEN');
            if ($singletoken) {
                // 从 Redis 获取 time
                $redisTime = \Redis::get(STRING_SINGLETOKEN_ . $userInfo->guid);
                // 重新获取加密参数加密
                $ip = $request->getClientIp();
                $secret = md5($ip . $userInfo->guid . $redisTime);
                if ($singletoken != $secret) {
                    // 记录此次异常登录记录
                    \DB::table('data_login_exception')->insert(['guid' => $userInfo->guid, 'ip' => $ip, 'addtime' => time()]);
                    // 清除 session 数据
                    \Session::forget('indexlogin');
                    return view('/403')->with(['Msg' => '您的帐号在另一个地点登录..']);
                }
                return $next($request);
            } else {
                return redirect('/login');
            }
        } else {
            return redirect('/login');
        }
    }


}
