<?php

namespace App\Http\Controllers\v1\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

class BaseController extends Controller
{
    use Helpers;
    /*返回加密数据*/
    public function reAec($data){
        $key = env('APP_KEY');
        if(is_object($data) == true){
            $data =$data;
        }else{
             $data= implode('|',$data);
        }


        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        return $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );


    }
}
