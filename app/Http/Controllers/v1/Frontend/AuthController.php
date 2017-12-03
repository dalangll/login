<?php

namespace App\Http\Controllers\v1\Frontend;

use App\Models\MemberInfo;
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
use App\Models\LoginRecord as Record;
use Illuminate\Support\Facades\Redis;
use Mail;
use App\Jobs\SendJob;

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
        $member_info = [
            'member_id'=>$user->id,
            'ip'=>getIp(),
            'address'=>getAddress()

        ];
       $memberinfo = MemberInfo::create($member_info);

        if(!$user){
            abort(500,'注册用户失败，请稍后尝试');
        }elseif($user && $memberinfo){
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
            $validator = Validator::make($request->all(), [

                'mobile' => 'required|between:11,11',
                'password' => 'required|between:6,20',

            ], [

                'mobile.required' => '手机号码不能为空',
                'password.required' => '密码不能为空',

            ]);

            /*数据验证失败*/
            if ($validator->fails()) {
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

        /*当前地区*/
        $ipAddress = getAddress();
        /*分隔出城市*/
        $ipAddress = explode(',',$ipAddress);
        $ipAddress = $ipAddress['2'];
        /*获得上次登录地址*/
        $address = Member::leftjoin('member_info','member.id','=','member_info.member_id')->where('member.mobile','=',$request->get('mobile'))->select('member_info.address')->get()->toArray();
        /*转换为字符串*/
        $last_addrees=$address['0']['address'];


        $time = Carbon::now()->toDateTimeString();

         /*解析出用户id*/ 
        $user = json_decode(base64_decode(explode('.', $token)[1]), true);

        $member = Member::find($user['sub']);

        /*判断该用户是否以封禁*/
        if($member->lock==true){
            abort('该账号以封禁，请联系客服');
        };

        if($ipAddress != $last_addrees){
            $validator = Validator::make($request->all(),[
                'sms' => 'required|sms:' . $request->get('mobile'),
            ],[
                'sms.required' => '短信验证码不能为空',
                'sms.sms' => '短信验证码错误',
            ]);
            /*数据验证失败*/
            if ($validator->fails()) {
                throw new StoreResourceFailedException("Validation Error", $validator->errors());exit;
            }

        }
        if($ipAddress != $last_addrees){
            /*发送异地登录通知短信*/
            $this->sendInform($member->mobile,$member->username,$time,$ipAddress);
        }

        /*用户登录信息*/
        $member_info = [
            'member_id'=>$user['sub'],
            'login_time'=>$time,
            'ip'=>getIp(),
            'address'=>$ipAddress
        ];
        MemberInfo::where('member_id',$user['sub'])->update($member_info);


        /*判断用户是否激活*/
       /* if($member->status==false){
            abort('该账号还未激活');
        }*/
        /*登录记录*/
        $loginrecord = [
            'uid'=>$member->id,
            'login_time'=>Carbon::now()
        ];
        Record::create($loginrecord);
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
        //setcookie('login',$token);
        LoginToken::create(['sub_id'=>$user['sub'],'token'=>$token]);
        return response($result,201)->withCookie('SINGLETOKEN', $token);
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

    /*testMail*/
    public function testMail(Request $request){
        /*数据验证字段*/
        $validator = Validator::make($request->all(),[
            'username'=>'required',
            'email'=>'required|email',
            'password'=>'required|between:6,20',

        ],[
            'username.required'=>'用户名不能为空',
            'email.required'=>'邮箱不能为空',
            'email.email' => '邮箱格式错误',
            'password.required'=>'密码不能为空',
        ]);
        /*对mobile进行验证*/
        $email = $request->get('email');
        $validator->after(function ($validator) use ($email) {
            if ($email) {
                if (Member::where('email', '=', $email)->count()) {
                    $validator->errors()->add('email', '该邮箱以注册');
                }
            }
        });

        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        $uuid = $this->uuid();

        /*获取数据*/
        $data = [
            'username'=>$request->get('username'),
            'email'=>$email,
            'password'=>bcrypt($request->get('password')),
            'uuid'=>$uuid
        ];
        /*写入数据表*/
        $user = Member::create($data);

        dispatch(new SendJob($user));

        return '邮件队列发送成功';
    }

    public function activateMail(Request $request){
       echo $request->getRequestUri();
    }

    /*账号激活*/

    public function act(Request $request){
      $id = $request->get('uid');
      $uuid = $request->get('uuid');

        $user = Member::find($id);
        if($uuid !=$user->uuid ){
            abort('403','非法操作');
        }
        if($user->status == true && $uuid==$user->uuid ){
            return '该账号已激活，无需重复激活';
        }
        $user->status = true;
        $user->save();

        return '激活成功';


    }
}
