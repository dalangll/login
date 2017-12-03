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
         $key = env('APP_KEY');
          $ciphertext= $request->all();
          Crypt::decrypt($ciphertext['category_name']);

        if(count($ciphertext)==0){
            return $next($request);
        }

         $str= implode('|',$ciphertext);
         $c = base64_decode($str);

         $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
         $iv = substr($c, 0, $ivlen);
         $hmac = substr($c, $ivlen, $sha2len=32);
         $ciphertext_raw = substr($c, $ivlen+$sha2len);
         $decryption = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
         $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
         if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
         {

             $request->merge(compact('decryption'));
             return $next($request);
         }
    }

    public function terminate($request, $response){


    }

}
