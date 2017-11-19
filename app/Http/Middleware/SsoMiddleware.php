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
    public function handle($request, Closure $next)
    {
        /*实例化redis*/
         $redis = Redis::connection();
        /*获得用户信息*/
        $member = JWTAuth::user();

        if($member) {
          $cookieToken = $_COOKIE['login'];
            /*解析出用户id*/
           $parseToken = json_decode(base64_decode(explode('.', $cookieToken)[1]), true);

           /*判断redis是否存在，不存在就从数据库拿*/
           if ($redis->exists($parseToken['sub'])) {
               $redisToken = $redis->get($parseToken['sub']);
           } else {
               $redisToken = LoginToken::select('token')->where('sub_id', '=', $parseToken['sub'])->get()->toArray();
               $redisToken = $redisToken['0']['token'];
           }
           /*如果不相等，注销登录*/
           if ($cookieToken != $redisToken) {
               /*清除cookie*/
               setcookie("login","",time()-1);

               Auth::logout();

           }
       }else{
           abort(401, '未授权该接口，请登录后操作');
       }
          LoginToken::where('sub_id','=',$parseToken['sub'])->delete();
            return $next($request);

    }
}
