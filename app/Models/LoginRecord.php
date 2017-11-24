<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginRecord extends Model
{
    /*指定表名*/
    protected $table = 'login_record';
    /*不可赋值字段*/
    protected $guarded = [];

}
