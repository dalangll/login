<?php
namespace Hongli\Alipay;

use AlipayTradePayContentBuilder;
use AlipayTradeService;
use AlipayTradePrecreateContentBuilder;
use AlipayTradeQueryContentBuilder;
use GuzzleHttp\Client;

class Alipay
{
   private static $sign_type;
   private static $alipay_public_key;
   private static $merchant_private_key;
   private static $app_id;
   private static $gatewayUrl;
   private static $notify_url;
   private static $outTradeNo;
   private static $subject;
   private static $totalAmount;
   private static $authCode;
   private static $timeExpress;

    public function __construct()
    {
        /*将client注入到控制器里*/
        $this->client = new Client();
    }

    public static function testpay(){
      return  $outTradeNo=date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $barPayRequestBuilder = new AlipayTradePayContentBuilder();
        $barPayRequestBuilder->setOutTradeNo($outTradeNo);
        $barPayRequestBuilder->setTotalAmount($totalAmount);
        $barPayRequestBuilder->setAuthCode($authCode);
        $barPayRequestBuilder->setTimeExpress($timeExpress);
        $barPayRequestBuilder->setSubject($subject);


    }

}