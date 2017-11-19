<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthMiddleware
{
    /**
     * The authentication guard factory instance.
     * Auth组件
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     * 依赖注入Auth组件
     * AdminAuthJwt constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     * 判断是否登录，如果不是则返回401错误
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'api')
    {
        /*判断是否是管理员登录*/
        if ($this->auth->guard($guard)->guest() || JWTAuth::decode(JWTAuth::getToken())->get('guard') != $guard) {
            abort(401, '未授权该接口，请登录后操作');
        }
        /*是管理员登录则跳转到下一步*/
        return $next($request);
    }

}
