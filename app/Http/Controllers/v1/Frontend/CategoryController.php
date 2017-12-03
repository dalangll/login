<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Models\Category;
use Validator;


class CategoryController extends BaseController
{

  public function postAec(Request $request){
      $key =env('APP_KEY');
      $url = "http://dalang.s1.natapp.cc/api/admin/category/create";
      $data = array(
          "category_name"=>"冒险家",
          "order"=>"8",
          "status"=>"1"
          );
      $new= implode('|',$data);
      $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
      $iv = openssl_random_pseudo_bytes($ivlen);
      $ciphertext_raw = openssl_encrypt($new, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
      $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
      $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $ciphertext);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
      curl_setopt($ch, CURLOPT_ENCODING, ""); //必须解压缩防止乱码
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; zh-CN) AppleWebKit/535.12 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/535.12");
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);

      $output = curl_exec($ch);
      curl_close($ch);

      print_r($output);
  }

}
