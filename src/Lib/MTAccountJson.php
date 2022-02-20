<?php


namespace AleeDhillon\MetaFive\Lib;


class MTAccountJson
{
    public static function GetFromJson($obj){
        if ($obj == null) return null;
        $result = new MTAccount();
        //---
        $result->Login             = (int)$obj->Login;
        $result->CurrencyDigits    = (int)$obj->CurrencyDigits;
        $result->Balance           = (float)$obj->Balance;
        $result->Credit            = (float)$obj->Credit;
        $result->Margin            = (float)$obj->Margin;
        $result->MarginFree        = (float)$obj->MarginFree;
        $result->MarginLevel       = (float)$obj->MarginLevel;
        $result->MarginLeverage    = (int)$obj->MarginLeverage;
        $result->Profit            = (float)$obj->Profit;
        $result->Storage           = (float)$obj->Storage;
        $result->Commission        = (float)$obj->Commission;
        $result->Floating          = (float)$obj->Floating;
        $result->Equity            = (float)$obj->Equity;
        $result->SOActivation      = (int)$obj->SOActivation;
        $result->SOTime            = (int)$obj->SOTime;
        $result->SOLevel           = (float)$obj->SOLevel;
        $result->SOEquity          = (float)$obj->SOEquity;
        $result->SOMargin          = (float)$obj->SOMargin;
        if(isset($obj->Assets))
            $result->Assets         = (float)$obj->Assets;
        if(isset($obj->Liabilities))
            $result->Liabilities    = (float)$obj->Liabilities;
        $result->BlockedCommission = (float)$obj->BlockedCommission;
        $result->BlockedProfit     = (float)$obj->BlockedProfit;
        $result->MarginInitial     = (float)$obj->MarginInitial;
        $result->MarginMaintenance = (float)$obj->MarginMaintenance;
        //---
        $obj = null;
        //---
        return $result;
    }
}
