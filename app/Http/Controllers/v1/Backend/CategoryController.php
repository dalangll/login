<?php

namespace App\Http\Controllers\v1\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Validator;
use Illuminate\Support\Facades\Crypt;
use Dingo\Api\Exception\StoreResourceFailedException;

class CategoryController extends BaseController
{
    /*新建分类*/
    public function create(Request $request){

       /* $data = $request->all();
        $info = $data['original_plaintext'];
        $info = explode('|',$info);*/
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
        $plaintext = Category::select('id','category_name','order','status','created_at','updated_at')->orderBy('order','desc')->get();
       return $data = Crypt::encrypt($plaintext);
       return $jsdata = Crypt::decrypt($data);
        /*返回数据*/
        return  $this->reAec($plaintext);

    }

    public function getidd(){
       /* $key = 'gdfgdfgdsdfsf===';
        $plaintext = "message to be encrypted";
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
         echo $ciphertext.'<br>';*/
//decrypt later....
        $key = env('APP_KEY');
        $ciphertext= $this->show();
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            return $original_plaintext;
        }
    }

    //convert object to array
    function object_to_array($obj){
        if(is_array($obj)){
            return $obj;
        }
        $_arr = is_object($obj)? get_object_vars($obj) :$obj;
            $arr = [];
        foreach ($_arr as $key => $val){
            $val=(is_array($val)) || is_object($val) ? object_to_array($val) :$val;
            $arr[$key] = $val;
        }

        return $arr;

    }


}
