<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Alipay;

class AlipayController extends Controller
{
    public function alipayf(){


        /*订单号*/
        $outTradeNo=date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        /*金额*/
        $totalAmount = '0.01';
        /*付款条码*/
        $authCode = '';
        /*支付超时*/
        $timeExpress = "5m";
        /*主体*/
        $subject = "大鱼海棠";

        $barPayRequestBuilder = new AlipayTradePayContentBuilder();

        $barPayRequestBuilder->setOutTradeNo($outTradeNo);
        $barPayRequestBuilder->setTotalAmount($totalAmount);
        $barPayRequestBuilder->setAuthCode($authCode);
        $barPayRequestBuilder->setTimeExpress($timeExpress);
        $barPayRequestBuilder->setSubject($subject);

        $barPay = new AlipayTradeService(config('alipay'));
        $barPayResult = $barPay->barPay($barPayRequestBuilder);

        switch ($barPayResult->getTradeStatus()) {
            case "SUCCESS":
                echo "支付宝支付成功:" . "<br>--------------------------<br>";
                print_r($barPayResult->getResponse());
                break;
            case "FAILED":
                echo "支付宝支付失败!!!" . "<br>--------------------------<br>";
                if (!empty($barPayResult->getResponse())) {
                    print_r($barPayResult->getResponse());
                }
                break;
            case "UNKNOWN":
                echo "系统异常，订单状态未知!!!" . "<br>--------------------------<br>";
                if (!empty($barPayResult->getResponse())) {
                    print_r($barPayResult->getResponse());
                }
                break;
            default:
                echo "不支持的交易状态，交易返回异常!!!";
                break;
        }
        return;

    }
}
