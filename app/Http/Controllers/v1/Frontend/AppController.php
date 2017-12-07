<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use Mrgoon\AliSms\AliSms;
use Illuminate\Support\Facades\Redis;
use Crypt;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Response;
use GuzzleHttp\Client;



class AppController extends BaseController
{
    /*发送短信验证码*/
    public function send(Request $request)
    {
        $mobile =$request->get('mobile');

        $sms = app(AliSms::class);
        $sms->sendSms($mobile, 'SMS_92580003', ['number' => $code]);
        /*验证码存入redis，过期时间为5分钟*/
        Redis::set('sms:'.$mobile, $code, 'EX', 300);

       return $code;

    }


    /*刷新token*/
    public function refreshToken()
    {
        /*获取refresh_token*/
        $member = JWTAuth::user();

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
        if ($id == $parseToken['sub'] && strtotime($refresh_ttl) > time()) {
            $newtoken = JWTAuth::refresh($token);
        } else {
            abort(500, 'token刷新失败，请稍后重试');
        }

        $result =
            [
                'data' => [
                    'id' => $parseToken['sub'],
                    'token_type' => 'Bearer',
                    'token' => $newtoken,
                    'refresh_token' => $refreshToken,
                    'expired_at' => Carbon::now()->addMinutes(config('jwt.ttl'))->toDateTimeString(),
                    'refresh_expired_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString()

                ]

            ];

        return response($result, 201);


    }

        /*根据两点的经纬度计算距离*/
        public function distance($lat1,$lat2,$lng1,$lng2)
        {

            //将角度转为狐度
            $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
            $radLat2 = deg2rad($lat2);
            $radLng1 = deg2rad($lng1);
            $radLng2 = deg2rad($lng2);
            $a = $radLat1 - $radLat2;
            $b = $radLng1 - $radLng2;
            $s = 2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6371;
            return round($s,1);
        }



}
