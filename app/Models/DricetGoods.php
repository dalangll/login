<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DricetGoods extends Model
{
    /*指定表名*/
    protected $table = 'goods';
    /*不可赋值字段*/
    protected $guarded =[];
}
