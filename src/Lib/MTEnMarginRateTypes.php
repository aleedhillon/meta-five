<?php


namespace AleeDhillon\MetaFive\Lib;


/**
 * Margin Rate Types
 * Class MTEnMarginRateTypes
 */
class MTEnMarginRateTypes
{
    const MARGIN_RATE_BUY             = 0;
    const MARGIN_RATE_SELL            = 1;
    const MARGIN_RATE_BUY_LIMIT       = 2;
    const MARGIN_RATE_SELL_LIMIT      = 3;
    const MARGIN_RATE_BUY_STOP        = 4;
    const MARGIN_RATE_SELL_STOP       = 5;
    const MARGIN_RATE_BUY_STOP_LIMIT  = 6;
    const MARGIN_RATE_SELL_STOP_LIMIT = 7;
    //--- enumeration borders
    const MARGIN_RATE_FIRST = MTEnMarginRateTypes::MARGIN_RATE_BUY;
    const MARGIN_RATE_LAST  = MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT;
}
