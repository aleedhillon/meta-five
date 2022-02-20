<?php


namespace AleeDhillon\MetaFive\Lib;
class MTOrderJson
{
    /**
     * Get MTOrder from json object
     * @param object $obj
     * @return MTOrder
     */
    public static function GetFromJson($obj)
    {
        if ($obj == null) return null;
        $info = new MTOrder();
        //---
        $info->Order = (int)$obj->Order;
        $info->ExternalID = (string)$obj->ExternalID;
        $info->Login = (int)$obj->Login;
        $info->Dealer = (int)$obj->Dealer;
        $info->Symbol = (string)$obj->Symbol;
        $info->Digits = (int)$obj->Digits;
        $info->DigitsCurrency = (int)$obj->DigitsCurrency;
        $info->ContractSize = (float)$obj->ContractSize;
        $info->State = (int)$obj->State;
        $info->Reason = (int)$obj->Reason;
        $info->TimeSetup = (int)$obj->TimeSetup;
        $info->TimeExpiration = (int)$obj->TimeExpiration;
        $info->TimeDone = (int)$obj->TimeDone;
        $info->TimeSetupMsc = (int)$obj->TimeSetupMsc;
        $info->TimeDoneMsc = (int)$obj->TimeDoneMsc;
        $info->ModifyFlags = (int)$obj->ModifyFlags;
        $info->Type = (int)$obj->Type;
        $info->TypeFill = (int)$obj->TypeFill;
        $info->TypeTime = (int)$obj->TypeTime;
        $info->PriceOrder = (float)$obj->PriceOrder;
        $info->PriceTrigger = (float)$obj->PriceTrigger;
        $info->PriceCurrent = (float)$obj->PriceCurrent;
        $info->PriceSL = (float)$obj->PriceSL;
        $info->PriceTP = (float)$obj->PriceTP;
        $info->VolumeInitial = (int)$obj->VolumeInitial;
        if (isset($obj->VolumeInitialExt))
            $info->VolumeInitialExt = (int)$obj->VolumeInitialExt;
        else
            $info->VolumeInitialExt = MTUtils::ToNewVolume($info->VolumeInitial);
        $info->VolumeCurrent = (int)$obj->VolumeCurrent;
        if (isset($obj->VolumeCurrentExt))
            $info->VolumeCurrentExt = (int)$obj->VolumeCurrentExt;
        else
            $info->VolumeCurrentExt = MTUtils::ToNewVolume($info->VolumeCurrent);
        $info->ExpertID = (float)$obj->ExpertID;
        $info->ExpertPositionID = (float)$obj->PositionID;
        $info->PositionByID = (float)$obj->PositionByID;
        $info->Comment = (string)$obj->Comment;
        $info->ActivationMode = (int)$obj->ActivationMode;
        $info->ActivationTime = (int)$obj->ActivationTime;
        $info->ActivationPrice = (float)$obj->ActivationPrice;
        $info->ActivationFlags = (int)$obj->ActivationFlags;
        //---
        return $info;
    }
}
