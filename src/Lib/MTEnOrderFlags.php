<?php


namespace AleeDhillon\MetaFive\Lib;


/**
 * allowed order flags
 * Class MTEnOrderFlags
 */
class MTEnOrderFlags
{
    const ORDER_FLAGS_NONE       = 0; // none
    const ORDER_FLAGS_MARKET     = 1; // market orders
    const ORDER_FLAGS_LIMIT      = 2; // limit orders
    const ORDER_FLAGS_STOP       = 4; // stop orders
    const ORDER_FLAGS_STOP_LIMIT = 8; // stop limit orders
    const ORDER_FLAGS_SL         = 16; // sl orders
    const ORDER_FLAGS_TP         = 32; // tp orders
    const ORDER_FLAGS_CLOSEBY    = 64; // close-by orders
    //--- all
    const ORDER_FLAGS_FIRST = MTEnOrderFlags::ORDER_FLAGS_MARKET;
    const ORDER_FLAGS_ALL   = 127; // ORDER_FLAGS_MARKET|ORDER_FLAGS_LIMIT|ORDER_FLAGS_STOP|ORDER_FLAGS_STOP_LIMIT|ORDER_FLAGS_SL|ORDER_FLAGS_TP|ORDER_FLAGS_CLOSEBY
}
