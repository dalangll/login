<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\v1\Frontend\BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use App\Models\Member;
use App\Models\LoginToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
class AuthController extends BaseController
{
	use RegistersUsers;
    use Helpers;
     // 用户注册
    public function register(Request $request){
    	/*数据验证字段*/
       $validator = Validator::make($request->all(),[
       	  'username'=>'required',
          'mobile' => 'required|phone',
          'sms' => 'required|sms:' . $request->get('mobile'),
          'password'=>'required|between:6,20',

       	],[
       	  'username.required'=>'用户名不能为空',
          'mobile.required'=>'手机号码不能为空',
          'mobile.phone' => '手机号码格式错误',
          'sms.required' => '短信验证码不能为空',
          'sms.sms' => '短信验证码错误',
          'password.required'=>'密码不能为空',
       	]);
        /*对mobile进行验证*/
        $mobile = $request->get('mobile');
        $validator->after(function ($validator) use ($mobile) {
            if ($mobile) {
                if (Member::where('mobile', '=', $mobile)->count()) {
                    $validator->errors()->add('mobile', '该手机号码以注册');
                }
            }
        });

         /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
     
         /*获取数据*/
        $data = [
            'username'=>$request->get('username'),
            'mobile'=>$mobile,
            'password'=>bcrypt($request->get('password'))
        ];
        /*写入数据表*/
        $user = Member::create($data);

        if(!$user){
            abort(500,'注册用户失败，请稍后尝试');
        }else{
             $info = [
                 'date'=>[
		            'id'=>$user->id,
		            'token_type'=>'Bearer',
		            'token'=>JWTAuth::fromUser($user),
		            'expired_at' => Carbon::now()->addMinutes(config('jwt.ttl'))->toDateTimeString(),
		            'refresh_expired_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString()
            ]
        ];
        return response($info, 201);
        }

    }

     /*登录*/
    public function login(Request $request){
    		/*数据验证字段*/
       $validator = Validator::make($request->all(),[
       	 
          'mobile'=>'required|between:11,11',
          'password'=>'required|between:6,20',

       	],[
    
          'mobile.required'=>'手机号码不能为空',
          'password.required'=>'密码不能为空',
       	]);
    	
          /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
       
        /*获取数据*/
        $info = [
        'mobile' => $request->get('mobile'),
        'password' => $request->get('password')
        ];
        if(!$token=JWTAuth::attempt($info)){
        	  $this->response->errorUnauthorized('用户账户或者密码错误');
        }
         
         /*解析出用户id*/ 
        $user = json_decode(base64_decode(explode('.', $token)[1]), true);

        $member = Member::find($user['sub']);
      
         /*组装数据*/
        $result['data'] = [
            'id' => $user['sub'],
            'token_type' => 'Bearer',
            'token' => $token,
            'refresh_token' => $this->getrefreshtoken($member->id),
            'expired_at' => Carbon::now()->addMinutes(config('jwt.ttl'))->toDateTimeString(),
            'refresh_expired_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString(),
        ];
        Redis::set($user['sub'],$token);
        setcookie('login',$token);
        LoginToken::create(['sub_id'=>$user['sub'],'token'=>$token]);
        return response($result,201);
    }
    public function getsms(){
       return Redis::get('sms');
    }
    /*退出登录*/
     public function destroy()
    {
        /*退出登录*/
        Auth::logout();
        /*返回空响应*/
        return $this->response->noContent();
    }
}
