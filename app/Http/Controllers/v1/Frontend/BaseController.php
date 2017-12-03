<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Crypt;
use Mrgoon\AliSms\AliSms;
use Dingo\Api\Routing\Helpers;
class BaseController extends Controller
{
    use Helpers;
    //获取refresh_token
   public function getrefreshtoken($id){

   	  $data = [
         'id'=>$id,
         'refresh_ttl'=> Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString()

   	  ];
   	return Crypt::encrypt($data);

   }

    protected function dataResponse($data, $status = 200)
    {
        /*组装数据*/
        $result = [
            'data' => $data
        ];
        /*响应*/
        return response($result, $status);
    }

   protected function uuid($prefix = ""){    //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;
    }



    /*异地登录短信通知*/
    public function sendInform($mobile,$name,$time,$address)
    {

        $sms = app(AliSms::class);
        $sms->sendSms($mobile, 'SMS_114070177', ['name' => $name,'time'=>$time,'address'=>$address]);
        if(!$sms){
            return '发送失败，请稍后重试';
        }


    }

    /*返回加密数据*/
    public function reAec($data){
        $key = env('APP_KEY');
        $new= implode('|',$data);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($new, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        return $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );


    }
}
