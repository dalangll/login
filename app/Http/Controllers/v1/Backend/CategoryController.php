<?php

namespace App\Http\Controllers\v1\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Validator;
use Dingo\Api\Exception\StoreResourceFailedException;

class CategoryController extends BaseController
{
    /*新建分类*/
    public function create(Request $request){

        $validator = Validator::make($request->all(),[
            'category_name'=>'required',
            'order'=>'required',
            'status'=>'required',
        ],[
            'category_name.required'=>'分类名不能为空',
            'order.required'=>'排序不能为空',
            'status.required'=>'分类状态不能为空'
        ]);

        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        /*获取数据*/
       $data =$request->only(['category_name','order','status']);
       /*执行写入*/
      $result = Category::create($data);

      if(!$result){
          abort('500','新建分类失败');
      }
        return $this->response->noContent();
    }

    /*修改分类*/
    public function update(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'category_name'=>'required',
            'order'=>'required',
            'status'=>'required',
        ],[
            'category_name.required'=>'分类名不能为空',
            'order.required'=>'排序不能为空',
            'status.required'=>'分类状态不能为空'
        ]);

        /*数据验证失败*/
        if($validator->fails()){
            throw new StoreResourceFailedException("Validation Error", $validator->errors());
        }
        /*获取数据*/
        $data =$request->only(['category_name','order','status']);

        $category =  Category::find($id);

        if(!$category){
            abort(404,'该分类不存在，请核实后重试');
        }
        $result = Category::where('id',$id)->update($data);

        if(!$result){
            abort(500,'更新分类失败，请稍后重试');
        }

        return $this->response->noContent();

    }
    /*分类列表*/
    public function show(){
         $result = Category::select('id','category_name','order','status','created_at','updated_at')->orderBy('order','desc')->get();

        /*返回数据*/
        return $result;
    }


}
