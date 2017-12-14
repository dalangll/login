<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DricetGoods;
use Carbon\Carbon;

class MemberOrder extends Model
{
    protected $table = 'member_order';

    protected $guarded = [];

    public $timestamps = false;

    /*生成订单*/
   static public function generationOrder($goods_id=null,$member_id=null){

         $goods = DricetGoods::find($goods_id);
         if(!$goods){
             abort(404, '该商品不存在，请核实后重试');
         }
         if($goods->status == false){
             abort(401,'该商品已下架');
         }
         if($goods->stock == false){
             abort(401,'该商品暂时无货，敬请等待');
         }

         $money = $goods->money;//商品价格
         $member = $member_id;//会员id
         $code =  date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);//订单号

         /*组装订单数据*/
           $arrcoed = [
               'out_trade_no'=>$code,
               'total_amount'=>$money,
               'subject'=>$member
           ];

           $arrorder = [
               'member_id'=>$member,
               'goods_id'=>$goods_id,
               'code'=>$code,
               'money'=>$money,
               'pay_type'=>'支付宝',
               'pay_time'=>Carbon::now()->toDateTimeString()
           ];


         self::create($arrorder);

         return $arrcoed;


    }
}
