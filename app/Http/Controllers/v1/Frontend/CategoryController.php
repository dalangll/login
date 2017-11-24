<?php

namespace App\Http\Controllers\v1\Frontend;

use Illuminate\Http\Request;
use App\Models\Category;
use Validator;


class CategoryController extends BaseController
{
    public function create(Request $request){
         $validator = Validator::make($request->all(),[
             'category_name'=>'required',

         ]);

    }
}
