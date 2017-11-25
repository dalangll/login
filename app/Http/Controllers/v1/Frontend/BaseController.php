<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Crypt;
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






}
