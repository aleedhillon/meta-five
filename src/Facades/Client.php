<?php

namespace AleeDhillon\MetaFive\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AleeDhillon\MetaTrader\MetaTrader
 */
class Client extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'meta-five';
    }
}
