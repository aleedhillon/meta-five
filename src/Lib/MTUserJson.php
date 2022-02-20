<?php


namespace AleeDhillon\MetaFive\Lib;


class MTUserJson
{
    public static function GetFromJson($obj)
    {
        if ($obj == null) return null;

        $result = new MTUser();
        //---
        $result->Login         = (int)$obj->Login;
        $result->Group         = (string)$obj->Group;
        $result->Rights        = (int)$obj->Rights;
        $result->Name          = (string)$obj->Name;
        $result->Company       = (string)$obj->Company;
        $result->Account       = (string)$obj->Account;
        $result->Country       = (string)$obj->Country;
        $result->Language      = (int)$obj->Language;
        $result->City          = (string)$obj->City;
        $result->State         = (string)$obj->State;
        $result->ZipCode       = (string)$obj->ZipCode;
        $result->Address       = (string)$obj->Address;
        $result->Phone         = (string)$obj->Phone;
        $result->Email         = (string)$obj->Email;
        $result->ClientID      = (int)$obj->ClientID;
        $result->ID            = (string)$obj->ID;
        $result->Status        = (string)$obj->Status;
        $result->Comment       = (string)$obj->Comment;
        $result->Color         = (int)$obj->Color;
        $result->PhonePassword = (string)$obj->PhonePassword;
        $result->Leverage      = (int)$obj->Leverage;
        $result->Agent         = (int)$obj->Agent;
        //---
        $result->CertSerialNumber = (int)$obj->CertSerialNumber;
        $result->Registration     = (int)$obj->Registration;
        $result->LastAccess       = (int)$obj->LastAccess;
        $result->LastPassChange    = (int)$obj->LastPassChange;
        $result->LastIP           = (string)$obj->LastIP;
        //---
        $result->Balance           = (float)$obj->Balance;
        $result->Credit            = (float)$obj->Credit;
        $result->InterestRate      = (float)$obj->InterestRate;
        $result->CommissionDaily   = (float)$obj->CommissionDaily;
        $result->CommissionMonthly = (float)$obj->CommissionMonthly;
        $result->BalancePrevDay    = (float)$obj->BalancePrevDay;
        $result->BalancePrevMonth  = (float)$obj->BalancePrevMonth;
        $result->EquityPrevDay     = (float)$obj->EquityPrevDay;
        $result->EquityPrevMonth   = (float)$obj->EquityPrevMonth;
        //---
        $result->MQID              = (string)$obj->MQID;
        $result->LeadSource        = (string)$obj->LeadSource;
        $result->LeadCampaign      = (string)$obj->LeadCampaign;
        //---
        $result->TradeAccounts     = (string)$obj->TradeAccounts;
        //---
        return $result;
    }
}
