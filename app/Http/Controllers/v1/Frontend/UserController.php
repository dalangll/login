<?php

namespace App\Http\Controllers\v1\Frontend;

use App\Models\LoginRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member;
use JWTAuth;
use DB;

class UserController extends BaseController
{
  /*封禁用户账号*/
  public function lock(Request $request,$id){

     $member= Member::find($id);
     if(!$member){
         abort('该账号不存在');
     }
     if($member->hongli_lock==false) {
         $member->hongli_lock = true;
         $member->save();
     }elseif($member->hongli_lock==true){
         $member->hongli_lock=false;
         $member->save();
     }
      return response("修改用户状态成功");
  }

  /*获取用户上次登录时间*/
  public function getLoginTime(){
      $member = JWTAuth::user();
      $id = $member->id;
      $time = DB::select("select login_time from login_record where uid = $id order by login_time desc limit 1,1");
      return $time;
  }
}
