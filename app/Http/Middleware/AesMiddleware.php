<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Crypt;
use Closure;

class AesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*获取客户端提交数据*/
         $ciphertext= $request->all();
         //dd(is_array($ciphertext));
         /*如果是get请求没提交数据等直接下一步*/
         if(empty($ciphertext)){

             return $next($request);
         }
         $dataes = $ciphertext['category_name'];
        /*将数据解密*/
        $key = env('APP_KEY');
        $c = base64_decode($dataes);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $decryption = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))
        {

            $goodsdata = json_decode($decryption,true);
            $request->attributes->add(compact('goodsdata'));
            return $next($request);
        }
    }

         public function terminate($request, $response){


    }

}
