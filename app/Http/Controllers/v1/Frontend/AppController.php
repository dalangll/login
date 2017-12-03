<?php

namespace App\Http\Controllers\v1\Frontend;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mrgoon\AliSms\AliSms;
use Illuminate\Support\Facades\Redis;
use Crypt;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use Response;
use GuzzleHttp\Client;
use App\Jobs\MyJob;

class AppController extends BaseController
{
    /*发送短信验证码*/
    public function send(Request $request)
    {
         $data = $request->all();

       // $ciphertext = $request->get('original_plaintext');
         //$mobile=implode('|',$ciphertext);
        return $data = $data['original_plaintext'];

        $code = rand(1000, 9999);

        $sms = app(AliSms::class);
        $sms->sendSms($ciphertext, 'SMS_92580003', ['number' => $code]);
        /*验证码存入redis，过期时间为5分钟*/
        Redis::set('sms:'.$ciphertext, $code, 'EX', 300);

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

    public function cookie(Request $request)
    {
        $ip = $request->getClientIps();
        return $ip;
    }

    public function getcookie()
    {
        $cookieToken = $_COOKIE['login'];
        if ($cookieToken) {
            $parseToken = json_decode(base64_decode(explode('.', $cookieToken)[1]), true);
            $redisToken = Redis::get($parseToken['sub']);
        }

    }

    function getIps()
    {

        $mainIp = '';
        if (getenv('HTTP_CLIENT_IP'))
            $mainIp = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $mainIp = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $mainIp = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $mainIp = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $mainIp = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $mainIp = getenv('REMOTE_ADDR');
        else
            $mainIp = 'UNKNOWN';
        return $mainIp;

    }

    function GetIpLookup($ip = '')
    {
        $ip = '183.14.133.47';
        if (empty($ip)) {

            $ip = GetIp();

        }

        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);

        if (empty($res)) {

            return false;

        }

        $jsonMatches = array();

        preg_match('#\{.+?\}#', $res, $jsonMatches);

        if (!isset($jsonMatches[0])) {

            return false;

        }

        $json = json_decode($jsonMatches[0], true);

        if (isset($json['ret']) && $json['ret'] == 1) {

            $json['ip'] = $ip;

            unset($json['ret']);

        } else {

            return false;

        }

        return $json;

    }





        public function distance()
        {
            $lat1='28.2340227593';
            $lat2='22.5485544122';
            $lng1='112.9453203518';
            $lng2='114.0661345267';
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


    public function aoliaddress()
    {

        $ip = '112.97.63.14';

        $aliaddress = "http://ip.taobao.com/service/getIpInfo.php?ip=$ip";

        $client = new \GuzzleHttp\Client;
        $ipdata = $client->get($aliaddress);


        /*$ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $aliaddress);//设置url属性
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        return $output = curl_exec($ch);//获取数据
        curl_close($ch);//关闭curl*/
        //dd(is_string($output));
        $result = json_decode($ipdata->getBody()->getContents(), true);

        /*判断结果是否为空*/
        if ($result['code'] == 0) {
            /*返回组装的ip和地址*/
            return $result['data']['country'] . ',' . $result['data']['region'] . ',' . $result['data']['city'] . ',' . $result['data']['ip'];
        }
    }

    public function testlogin(Request $request){
        return $request->all();
    }




}
