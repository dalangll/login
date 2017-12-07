<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Pay;

class PayController extends Controller
{
    public function testpay(){

        //获取前台传递参数并组合参数
        $data['payKey']= 'a0637c44f78b4026abfe9e918bdee530';  //payKey
        $data['paySecret']='cf3f8406623442fd8b4174c23e7ac521' ;  //paySecret
        $data['orderPrice']='4.00'; //金额 订单金额，单位：元保留小数点后两位
        $data['outTradeNo']=date('YmdHis'); //订单号商户支付订单号String
        $data['productType']= '10000201'; //产品类型，查阅本文档2.6
        $data['orderTime']=date('YmdHis') ; //下单时间
        $data['productName']='红狸购物'; //支付产品名称
        $data['orderIp']=getIp(); //下单IP
        $data['returnUrl']= "http://dalang.s1.natapp.cc/api/verfly"; //页面通知地址    支付完成后跳转的地址
        $data['notifyUrl']= "http://dalang.s1.natapp.cc/api/notifly"; //后台异步通知   是付款后银行会通知信息的回复
        $data['subPayKey']= ''; //子商户支付Key
        // $data['remark']='ok' ; //备注

        //拼接签名数据
        $zd="notifyUrl=".$data['notifyUrl']."&orderIp=".$data['orderIp']."&orderPrice=".$data['orderPrice']."&orderTime=".$data['orderTime']."&outTradeNo=".$data['outTradeNo']."&payKey=".$data['payKey']."&productName=".$data['productName']."&productType=".$data['productType']."&returnUrl=".$data['returnUrl']."&paySecret=".$data['paySecret'];
        // dd($zd);
        //拼接字符串，里面的拼接字段必须全部用上。
        //按照签名规则签名
        $data['sign']=strtoupper(md5($zd));    //MD5值必须大写
        unset($data['paySecret']);

        //支付地址
        $url="https://gateway.iexbuy.cn/cnpPay/initPay";

        //http请求发送数据
        // $res=$this->postArrayCurl($url,$data);
        $client = new Client();
        $response = $client->request('POST', $url, [
            "form_params" => $data
        ]);
        $data = $response->getBody();

        $data = json_decode($data, true);
//       return $data;
        return $data['payMessage'];



    }

    public function verfly(){
        return 'pay ok';
    }
    public function notifly(Request $request){
        $payKe = $request->get('payKey');
        $orderPrice = $request->get('orderPrice');
        $outTradeNo =$request->get('outTradeNo');
        $productType = $request->get('productType');
        $orderTime =$request->get('orderTime');
        $productName = $request->get('productName');
        $tradeStatus =$request->get('tradeStatus');
        $sign=$request->get('sign ');

        $successTime = $request->get('successTime');
        $remark=$request->get('remark');
        $trxno =$request->get('trxNo');

        $paySecret='cf3f8406623442fd8b4174c23e7ac521';
        $ip = getIp();
        $returnUrl ="http://dalang.s1.natapp.cc/api/verfly";
        $notifyUrl ="http://dalang.s1.natapp.cc/api/notifly";


        $zd="notifyUrl=".$notifyUrl."&orderIp=".$ip."&orderPrice=".$orderPrice."&orderTime=".$orderTime."&outTradeNo=".$outTradeNo."&payKey=".$payKe."&productName=".$productName."&productType=".$productType."&returnUrl=".$returnUrl."&paySecret=".$paySecret;

      if($sign!=$zd){
          return 'FAILED';
      }
      if($tradeStatus == 'SUCCESS'){
          return 'SUCCESS';
      }else{
          return 'FAILED';
      }


    }
    public function pay(){
        $order = [
            'out_trade_no' => date('YmdHis').rand(10000,99999),
            'total_amount' => '998',
            'subject' => '',
            //'qr_pay_mode'=>1
        ];
        return Pay::driver('alipay')->gateway('web')->pay($order);

    }
}
