<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LoginToken;
use JWTAuth;
use Auth;
use Illuminate\Support\Facades\Redis;
class SsoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
   // public function handle($request, Closure $next)
  //  {
        /*实例化redis*/
      //   $redis = Redis::connection();
        /*获得用户信息*/
      //  $member = JWTAuth::user();

      //  if($member) {
       //     $cookieToken = $request->cookie('SINGLETOKEN');
            /*解析出用户id*/
        //   $parseToken = json_decode(base64_decode(explode('.', $cookieToken)[1]), true);

           /*判断redis是否存在，不存在就从数据库拿*/
         //  if ($redis->exists($parseToken['sub'])) {
            //   $redisToken = $redis->get($parseToken['sub']);
        //   } else {
            //    $redisToken = LoginToken::select('token')->where('sub_id', '=', $parseToken['sub'])->get()->toArray();
          //     $redisToken = $redisToken['0']['token'];
         //  }
           /*如果不相等，注销登录*/
        //   if ($cookieToken != $redisToken) {
               /*清除cookie*/
           //    setcookie("SINGLETOKEN","",time()-1);

          //     Auth::logout();

         //  }
      // }else{
      //     abort(401, '未授权该接口，请登录后操作');
    //   }
     //     LoginToken::where('sub_id','=',$parseToken['sub'])->delete();
      //      return $next($request);

   // }
    public function handle($request, Closure $next)
    {

        /*获得用户信息*/
        $member = JWTAuth::user();
        /*实例化redis*/
        $redis = Redis::connection();
        /*判断该key的值是否存在*/
        if($redis->exists($member->id)){
            /*得到redis值*/
            $redisToken = $redis->get($member->id);
            /*得到当前登录token*/
            $userToken = JWTAuth::getToken();
            if($redisToken == $userToken){
                return $next($request);
            }else{

                JWTAuth::refresh($userToken);
                abort('您的账号在别处登录');
            }
        }


    }
}
