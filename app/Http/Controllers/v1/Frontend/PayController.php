<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Pay;
use Log;
use Carbon\Carbon;
use App\Models\DricetGoods;
use App\Models\MemberOrder;
use JWTAuth;


class PayController extends BaseController
{
    /*同步回调跳转测试*/
    public function verfly(){
        return 'pay ok';
    }

    /*支付异步回调*/
    public function notifly(Request $request){

            // 对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况
            // Log::info($request->trade_status);
        switch ($request->trade_status) {
            case 'TRADE_SUCCESS':
            case 'TRADE_FINISHED':
                $out_trade_no = $request->out_trade_no;
                $order = MemberOrder::where('code', $out_trade_no)->first();
                /*判断订单好是否正确*/
                if (!$order) {
                    abort(400, '订单不存在，请核实后重试');
                }
                /*判断订单金额是否一致*/
                if ($request->buyer_pay_amount != $order->money) {
                    abort(403, '订单金额出现异常，请稍后重试');
                }
                /*验证商户app_id*/
                if ($request->app_id != config('pay')['alipay']['app_id']) {
                    abort(403, '商户异常，请稍后重试');
                }
                /*验证商户uid*/
                if ($request->seller_id != 2088102174794153) {
                    abort(403, '商户异常，请稍后重试');
                }
                /*修改订单状态*/
                $order->pay_status = true;
                $order->success_time = Carbon::now()->toDateTimeString();
                $order->save();
                /*日志*/
                Log::info($request->all());
                echo "success";
        }

        }
    /*发起支付*/
    public function pay(){

        /*登录会员信息*/
       // $member = JWTAuth::user();
        /*得到订单信息*/
        $order = MemberOrder::generationOrder(22,51);
        /*请求支付*/
        return Pay::driver('alipay')->gateway('web')->pay($order);

    }

}
