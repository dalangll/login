<?php

namespace Hongli\Alipay\facades;

use Illuminate\Support\facades\Facade;

class Alipay extends Facade
{
    /**
     * Return facade accessor
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'alipay.base';
    }
}