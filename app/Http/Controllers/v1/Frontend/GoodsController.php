<?php

namespace App\Http\Controllers\v1\Frontend;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\DricetGoods;
class GoodsController extends Controller
{
    /*获取所有分类*/
    public function showcategory(Request $request){
      $category =  Category::select('id','category_name','order','status')->orderBy('order','desc')->get();
      if(!$category){
          abort(404,'分类未找到');
      }
      return $this->dataResponse($category);
    }
    /*获取某个分类下的所有商品*/
    public function categorygoods(Request $request,$id){
        /*验证数据格式*/
        $validator = Validator::make($request->all(), [
            'type' => 'integer|in:1,2,3'
        ], [
            'type.integer' => '过滤器类型格式错误',
            'type.in' => '过滤器类型必须为1,2或3'
        ]);
        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        $type = $request->get('type')??1;
        $builder= DricetGoods::where('category_id',$id)->select( 'id','goods_name', 'money', 'original_money', 'thumb' , 'status', 'sale', 'stock', 'order');
      switch ($type){
          /*默认人气降序*/
          case 1:
              $builder->orderBy('sale','desc');
              break;
           /*价格降序*/
          case 2:
              $builder->orderBy('money','desc');
              break;
          /*价格升序*/
          case 3:
              $builder->orderBy('money','asc');
      }
          $data = $builder->paginate(15);

        /*返回响应*/
        return $data;

    }

    /*获得所有商品*/
    public function showGoods(Request $request){
        /*验证数据格式*/
        $validator = Validator::make($request->all(), [
            'type' => 'integer|in:1,2,3'
        ], [
            'type.integer' => '过滤器类型格式错误',
            'type.in' => '过滤器类型必须为1,2或3'
        ]);
        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        $type = $request->get('type')??1;

        $builder= DricetGoods::select( 'id','goods_name', 'money', 'original_money', 'thumb' , 'status', 'sale', 'stock', 'order');

        switch ($type){
            /*默认人气降序*/
            case 1:
                $builder->orderBy('sale','desc');
                break;
            /*价格降序*/
            case 2:
                $builder->orderBy('money','desc');
                break;
            /*价格升序*/
            case 3:
                $builder->orderBy('money','asc');

        }
        /*得到数据*/
        $data = $builder->paginate(15);

        return $data;
    }
    /*商品详情*/
    public function particulars($id){
        /*执行查询*/
       $goods = DricetGoods::where('id',$id)->select('id','goods_name','money','original_money','banner','description','status','sale','stock')->first();
       /*响应*/
        /*将banners进行处理*/
        $goods->banners = explode('|', $goods->banners);

       $data['data']=$goods;

       return response($data);

    }

    /*商品图文详情*/
    public function tuenParticulars(Request $request,$id){

        $validator = Validator::make($request->all(),[
            'type' => 'integer|in:1,2'
        ], [
            'type.integer' => '过滤器类型格式错误',
            'type.in' => '过滤器类型必须为1,2'

        ]);
        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        /*默认为微信端详情*/
        $type = $request->get('type')??1;

       /*查询*/
       $builder = DricetGoods::where('id',$id)->where('status',true);

       switch ($type){
           case 1:
              $goods = $builder->select('id','content')->first();
               break;
           case 2:
              $goods =  $builder->select('id','content_app')->first();
               break;
       }
       /*判断商品是否存在*/


       if(!$goods){
           abort(404,'该商品不存在，请稍后重试');
       }

       /*判断是1还是2对详情分割*/
       if($type == 1){
           $goods->content = explode('|',$goods->content);

       }elseif ($type == 2){
          $goods->content_app = explode('|',$goods->content_app);
       }


       return $goods;

    }
    /**/
}
