<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Crypt;
class BaseController extends Controller
{
    //获取refresh_token
   public function getrefreshtoken($id){

   	  $data = [
         'id'=>$id,
         'refresh_ttl'=> Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString()

   	  ];
   	return Crypt::encrypt($data);

   }







}
