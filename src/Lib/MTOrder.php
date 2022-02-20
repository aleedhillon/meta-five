<?php


namespace AleeDhillon\MetaFive\Lib;


/**
 * Order information
 */
class MTOrder
{
    //--- order ticket
    public $Order;
    //--- order ticket in external system (exchange, ECN, etc)
    public $ExternalID;
    //--- client login
    public $Login;
    //--- processed dealer login (0-means auto)
    public $Dealer;
    //--- order symbol
    public $Symbol;
    //--- price digits
    public $Digits;
    //--- currency digits
    public $DigitsCurrency;
    //--- contract size
    public $ContractSize;
    //--- MTEnOrderState
    public $State;
    //--- MTEnOrderReason
    public $Reason;
    //--- order setup time
    public $TimeSetup;
    //--- order expiration
    public $TimeExpiration;
    //--- order filling/cancel time
    public $TimeDone;
    //--- order setup time in msc since 1970.01.01
    public $TimeSetupMsc;
    //--- order filling/cancel time in msc since 1970.01.01
    public $TimeDoneMsc;
    //--- modification flags (type is MTEnOrderTradeModifyFlags)
    public $ModifyFlags;
    //--- MTEnOrderType
    public $Type;
    //--- MTEnOrderFilling
    public $TypeFill;
    //--- MTEnOrderTime
    public $TypeTime;
    //--- order price
    public $PriceOrder;
    //--- order trigger price (stop-limit price)
    public $PriceTrigger;
    //--- order current price
    public $PriceCurrent;
    //--- order SL
    public $PriceSL;
    //--- order TP
    public $PriceTP;
    //--- order initial volume
    public $VolumeInitial;
    //--- order initial volume
    public $VolumeInitialExt;
    //--- order current volume
    public $VolumeCurrent;
    //--- order current volume
    public $VolumeCurrentExt;
    //--- expert id (filled by expert advisor)
    public $ExpertID;
    //--- expert position id (filled by expert advisor)
    public $ExpertPositionID;
    //--- position by id
    public $PositionByID;
    //--- order comment
    public $Comment;
    //--- order activation state (type is MTEnOrderActivation)
    public $ActivationMode;
    //--- order activation time
    public $ActivationTime;
    //--- order activation  price
    public $ActivationPrice;
    //--- order activation flag (type is MTEnTradeActivationFlags)
    public $ActivationFlags;
}
