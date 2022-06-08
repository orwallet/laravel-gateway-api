<?php

namespace Btph\GatewaySdk\Facade;

use Illuminate\Support\Facades\Facade;

class Gateway extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "gateway";
    }
}
