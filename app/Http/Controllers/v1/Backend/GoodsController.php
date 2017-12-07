<?php

namespace App\Http\Controllers\v1\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DricetGoods;
use Validator;

class GoodsController extends BaseController
{
    /*添加商品*/
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'goods_name' => 'required',
            'money' => 'required',
            'original_money' => 'required',
            'thumb' => 'required',
            'banner' => 'required',
            'description' => 'required',
            'status' => 'required',
            'sale' => 'required',
            'stock' => 'required',
            'order' => 'required'
        ], [
            'category_id.required' => '商品分类不能为空',
            'goods_name.required' => '商品标题不能为空',
            'money.required' => '商品价格不能为空',
            'original.required' => '商品原价不能为空',
            'thumb.required' => '商品缩略图不能为空',
            'banner.required' => '商品banner不能为空',
            'description.required' => '商品描述不能为空',
            'status.required' => '商品状态不能为空',
            'sale.required' => '是否推荐不能为空',
            'stock.required' => '库存不能为空',
            'order.required' => '商品排序不能为空'
        ]);

        /*数据验证失败*/
        if ($validator->fails()) {
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }

        /*获取数据*/
        $goodsdata = $request->only(['category_id', 'goods_name', 'money', 'original_money', 'thumb', 'banner', 'description', 'status', 'sale', 'stock', 'order']);
        /*执行写入*/

        $result = DricetGoods::create($goodsdata);

        if (!$result) {
            abort(500, '商品创建失败，请稍后重试');
        }
        return $this->response->noContent();
    }
    /*更新商品*/
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'category_id'=>'required',
            'goods_name'=>'required',
            'money'=>'required',
            'original_money'=>'required',
            'thumb'=>'required',
            'banner'=>'required',
            'description'=>'required',
            'status'=>'required',
            'sale'=>'required',
            'stock'=>'required',
            'order'=>'required'
        ],[
            'category_id.required'=>'商品分类不能为空',
            'goods_name.required'=>'商品标题不能为空',
            'money.required'=>'商品价格不能为空',
            'original.required'=>'商品原价不能为空',
            'thumb.required'=>'商品缩略图不能为空',
            'banner.required'=>'商品banner不能为空',
            'description.required'=>'商品描述不能为空',
            'status.required'=>'商品状态不能为空',
            'sale.required'=>'是否推荐不能为空',
            'stock.required'=>'库存不能为空',
            'order.required'=>'商品排序不能为空'
        ]);

        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        $goodsdata =  $request->only(['category_id','goods_name','money','original_money','thumb','banner','description','status','sale','stock','order']);
        /*数据更新*/
        DricetGoods::where('id',$id)->update($goodsdata);

        return $this->response->noContent();

    }
    /*商品删除*/
    public function delete($id){

           $goodsdata =  DricetGoods::where('id',$id)->delete();
           if($goodsdata){
                      abort(500,'删除失败，请稍后重试');
           }

           return $this->response->noContent();
    }

    /*商品列表*/
    public function listgoods(Request $request){
        $validator = Validator::make($request->all(),[
            'status'=>'in:1,2',
            'invent'=>'in:1,2,3',
            'order'=>'in:1,2,3,4,5,6',


        ],[
            'status.required'=>'商品状态不能为空',
            'invent.required'=>'库存状态不能为空',
            'order.required'=>'商品排序不能为空',
        ]);

        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        /*默认属性*/
        $status = $request->get('status')??1;
        $invent = $request->get('invent')??1;
        $order = $request->get('order')??1;

        /*执行查询*/
       $builder = DricetGoods::leftjoin('category','goods.category_id','=','category.id')->select('goods.id','goods.goods_name','category.category_name','money','original_money','sale','stock');

       switch ($status){
           /*上架状态*/
           case 1:
               $builder->where('goods.status',true);
               break;
           /*下架状态*/
           case 2:
               $builder->where('goods.status',false);
       }
       switch ($invent){
           /*有库存*/
           case 2:
               $builder->where('goods.stock',true);
               break;
            /*无库存*/
           case 3:
               $builder->where('goods.stock',false);
               break;
       }
       switch ($order){
           case 1:
               $builder->orderBy('goods.id','desc');
               break;
           case 2:
               $builder->orderBy('goods.id','asc');
               break;
           case 3:
               $builder->orderBy('goods.sale','desc');
               break;
           case 4:
               $builder->orderBy('goods.sale','asc');
               break;
           case 5:
               $builder->orderBy('goods.money','desc');
               break;
           case 6:
               $builder->orderBy('goods.money','asc');
       }
       /*查出数据*/
      $datas = $builder->paginate(15);

      $goods['data']=$datas;

      return $goods;

    }

    /*下架商品*/
    public function lowerframe(Request $request,$id){

       $goods = DricetGoods::find($id);

       $goods->status=false;

      if($goods->save()){
         return $this->response->noContent();
       }else {

          abort(500,'下架商品失败，请稍后重试');
      }
}
    /*复制新建商品*/
    public function copyGood(Request $request,$id){
       $good = DricetGoods::find($id);

       if(!$good){
           abort(404,'该商品不存在，请稍后重试');
       }
       $good=$good->toArray();
       /*去除id*/
       unset($good['id']);
       /*写入*/
      $copygoods =  DricetGoods::create($good);
      if(!$copygoods){
          abort(500,'复制新建商品失败');
      }

      /*返回响应*/
      return $this->response->noContent();

    }
    /*重置价格*/
    public function resetPrice(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'money'=>'required'
        ],[
            'money.required'=>'价格不能为空'
        ]);

        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }

        $reset = DricetGoods::find($id);
        $reset->money = $request->get('money');
        if($reset->save()){
            return $this->response->noContent();
        }else{
            abort(500,'重置价格失败');
        }
    }

}
