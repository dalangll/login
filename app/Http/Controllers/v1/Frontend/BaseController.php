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







}
