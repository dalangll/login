<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Models\Category;
use Validator;


class CategoryController extends BaseController
{

    /*获取所有分类*/
    public function showcategory(Request $request){
        $category =  Category::select('id','category_name','order','status')->orderBy('order','desc')->get();
        if(!$category){
            abort(404,'分类未找到');
        }
        return $this->dataResponse($category);
    }


}
