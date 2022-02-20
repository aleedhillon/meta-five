<?php
namespace AleeDhillon\MetaFive\Lib;

class MTOrderProtocol
{
    private $m_connect; // connection to MT5 server

    /**
     * @param MTConnect $connect - connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->m_connect = $connect;
    }

    /**
     * Get order
     * @param string $ticket - number of ticket
     * @param MTOrder $order
     * @return MTRetCode
     */
    public function OrderGet($ticket, &$order)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_TICKET => $ticket);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_ORDER_GET, $data)) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send order get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer order get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseOrder(MTProtocolConsts::WEB_CMD_ORDER_GET, $answer, $order_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse order get failed: ['.$error_code.']'.MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $order = $order_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command
     * @param string $answer - answer from server
     * @param MTOrderAnswer $order_answer
     * @return MTRetCode
     */
    private function ParseOrder($command, &$answer, &$order_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
        //---
        $order_answer = new MTOrderAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $order_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($order_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
        //--- get json
        if (($order_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     * @param string $answer - answer from server
     * @param MTOrderPageAnswer $order_answer
     * @return MTRetCode
     */
    private function ParseOrderPage(&$answer, &$order_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != MTProtocolConsts::WEB_CMD_ORDER_GET_PAGE) return MTRetCode::MT_RET_ERR_DATA;
        //---
        $order_answer = new MTOrderPageAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $order_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($order_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
        //--- get json
        if (($order_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get total order for login
     * @param string $login - user login
     * @param int $total - count of users orders
     * @return MTRetCode
     */
    public function OrderGetTotal($login, &$total)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_ORDER_GET_TOTAL, $data)) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send order get total failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer order get total is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseOrderTotal($answer, $order_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse order get total failed: ['.$error_code.']'.MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get total
        $total = $order_answer->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get order
     * @param int $login - number of ticket
     * @param int $offset - begin records number
     * @param int $total - total records need
     * @param array(MTOrder) $orders
     * @return MTRetCode
     */
    public function OrderGetPage($login, $offset, $total, &$orders)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
        //---
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_ORDER_GET_PAGE, $data)) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send order get page failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer order get page is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseOrderPage($answer, $order_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse order get page failed: ['.$error_code.']'.MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $orders = $order_answer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     * @param  $answer string server answer
     * @param  $order_answer MTOrderTotalAnswer
     * @return false
     */
    private function ParseOrderTotal(&$answer, &$order_answer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->m_connect->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_ORDER_GET_TOTAL) return MTRetCode::MT_RET_ERR_DATA;
        //---
        $order_answer = new MTOrderTotalAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $order_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $order_answer->Total = (int)$param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($order_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
        //---
        return MTRetCode::MT_RET_OK;
    }
}

/**
 * order types
 */
class MTEnOrderType
{
    const OP_BUY             = 0; // buy order
    const OP_SELL            = 1; // sell order
    const OP_BUY_LIMIT       = 2; // buy limit order
    const OP_SELL_LIMIT      = 3; // sell limit order
    const OP_BUY_STOP        = 4; // buy stop order
    const OP_SELL_STOP       = 5; // sell stop order
    const OP_BUY_STOP_LIMIT  = 6; // buy stop limit order
    const OP_SELL_STOP_LIMIT = 7; // sell stop limit order
    const OP_CLOSE_BY        = 8; // close by
    //--- enumeration borders
    const OP_FIRST = MTEnOrderType::OP_BUY;
    const OP_LAST  = MTEnOrderType::OP_CLOSE_BY;
}

/**
 * order filling types
 */
class MTEnOrderFilling
{
    const ORDER_FILL_FOK    = 0; // fill or kill
    const ORDER_FILL_IOC    = 1; // immediate or cancel
    const ORDER_FILL_RETURN = 2; // return order in queue
    //--- enumeration borders
    const ORDER_FILL_FIRST = MTEnOrderFilling::ORDER_FILL_FOK;
    const ORDER_FILL_LAST  = MTEnOrderFilling::ORDER_FILL_RETURN;
}

/**
 * order expiration types
 */
class MTEnOrderTime
{
    const ORDER_TIME_GTC           = 0; // good till cancel
    const ORDER_TIME_DAY           = 1; // good till day
    const ORDER_TIME_SPECIFIED     = 2; // good till specified
    const ORDER_TIME_SPECIFIED_DAY = 3; // good till specified day
    //--- enumeration borders
    const ORDER_TIME_FIRST = MTEnOrderTime::ORDER_TIME_GTC;
    const ORDER_TIME_LAST  = MTEnOrderTime::ORDER_TIME_SPECIFIED_DAY;
}

/**
 * order state
 */
class MTEnOrderState
{
    const ORDER_STATE_STARTED        = 0; // order started
    const ORDER_STATE_PLACED         = 1; // order placed in system
    const ORDER_STATE_CANCELED       = 2; // order canceled by client
    const ORDER_STATE_PARTIAL        = 3; // order partially filled
    const ORDER_STATE_FILLED         = 4; // order filled
    const ORDER_STATE_REJECTED       = 5; // order rejected
    const ORDER_STATE_EXPIRED        = 6; // order expired
    const ORDER_STATE_REQUEST_ADD    = 7;
    const ORDER_STATE_REQUEST_MODIFY = 8;
    const ORDER_STATE_REQUEST_CANCEL = 9;
    //--- enumeration borders
    const ORDER_STATE_FIRST = MTEnOrderState::ORDER_STATE_STARTED;
    const ORDER_STATE_LAST  = MTEnOrderState::ORDER_STATE_REQUEST_CANCEL;
}

/**
 * order activation state
 */
class MTEnOrderActivation
{
    const ACTIVATION_NONE       = 0; // none
    const ACTIVATION_PENDING    = 1; // pending order activated
    const ACTIVATION_STOPLIMIT  = 2; // stop-limit order activated
    const ACTIVATION_EXPIRATION = 3;
    const ACTIVATION_STOPOUT    = 4;  // order activate for stop-out
    //--- enumeration borders
    const ACTIVATION_FIRST = MTEnOrderActivation::ACTIVATION_NONE;
    const ACTIVATION_LAST  = MTEnOrderActivation::ACTIVATION_STOPOUT;
}

/**
 * order creation reasons
 */
class MTEnOrderReason
{
    const ORDER_REASON_CLIENT           = 0;  // order placed manually
    const ORDER_REASON_EXPERT           = 1;  // order placed by expert
    const ORDER_REASON_DEALER           = 2;  // order placed by dealer
    const ORDER_REASON_SL               = 3;  // order placed due SL
    const ORDER_REASON_TP               = 4;  // order placed due TP
    const ORDER_REASON_SO               = 5;  // order placed due Stop-Out
    const ORDER_REASON_ROLLOVER         = 6;  // order placed due rollover
    const ORDER_REASON_EXTERNAL_CLIENT  = 7;  // order placed from the external system by client
    const ORDER_REASON_VMARGIN          = 8;  // order placed due variation margin
    const ORDER_REASON_GATEWAY          = 9;  // order placed by gateway
    const ORDER_REASON_SIGNAL           = 10; // order placed by signal service
    const ORDER_REASON_SETTLEMENT       = 11; // order placed by settlement
    const ORDER_REASON_TRANSFER         = 12; // order placed due transfer
    const ORDER_REASON_SYNC             = 13; // order placed due synchronization
    const ORDER_REASON_EXTERNAL_SERVICE = 14; // order placed from the external system due service issues
    const ORDER_REASON_MIGRATION        = 15; // order placed due account migration from MetaTrader 4 or MetaTrader 5
    const ORDER_REASON_MOBILE           = 16; // order placed manually by mobile terminal
    const ORDER_REASON_WEB              = 17; // order placed manually by web terminal
    const ORDER_REASON_SPLIT            = 18; // order placed due split
    //--- enumeration borders
    const ORDER_REASON_FIRST = MTEnOrderReason::ORDER_REASON_CLIENT;
    const ORDER_REASON_LAST  = MTEnOrderReason::ORDER_REASON_SPLIT;
}

/**
 * order activation flags
 */
class MTEnTradeActivationFlags
{
    const ACTIV_FLAGS_NO_LIMIT      = 0x01;
    const ACTIV_FLAGS_NO_STOP       = 0x02;
    const ACTIV_FLAGS_NO_SLIMIT     = 0x04;
    const ACTIV_FLAGS_NO_SL         = 0x08;
    const ACTIV_FLAGS_NO_TP         = 0x10;
    const ACTIV_FLAGS_NO_SO         = 0x20;
    const ACTIV_FLAGS_NO_EXPIRATION = 0x40;
    //---
    const ACTIV_FLAGS_NONE = 0x00;
    const ACTIV_FLAGS_ALL  = 0x7F;
}

/**
 * modification flags
 */
class MTEnOrderTradeModifyFlags
{
    const MODIFY_FLAGS_ADMIN          = 0x001;
    const MODIFY_FLAGS_MANAGER        = 0x002;
    const MODIFY_FLAGS_POSITION       = 0x004;
    const MODIFY_FLAGS_RESTORE        = 0x008;
    const MODIFY_FLAGS_API_ADMIN      = 0x010;
    const MODIFY_FLAGS_API_MANAGER    = 0x020;
    const MODIFY_FLAGS_API_SERVER     = 0x040;
    const MODIFY_FLAGS_API_GATEWAY    = 0x080;
    const MODIFY_FLAGS_API_SERVER_ADD = 0x100;
    //--- enumeration borders
    const MODIFY_FLAGS_NONE = 0x000;
    const MODIFY_FLAGS_ALL  = 0x1FF;
}

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

/**
 * Answer on request order_get_total
 */
class MTOrderTotalAnswer
{
    public $RetCode = '-1';
    public $Total   = 0;
}

/**
 * get order page answer
 */
class MTOrderPageAnswer
{
    public $RetCode    = '-1';
    public $ConfigJson = '';

    /**
     * From json get class MTOrder
     * @return array(MTOrder)
     */
    public function GetArrayFromJson()
    {
        $objects = MTJson::Decode($this->ConfigJson);
        if ($objects == null) return null;
        $result = array();
        //---
        foreach ($objects as $obj) {
            $info = MTOrderJson::GetFromJson($obj);
            //---
            $result[] = $info;
        }
        //---
        $objects = null;
        //---
        return $result;
    }
}

/**
 * get order page answer
 */
class MTOrderAnswer
{
    public $RetCode    = '-1';
    public $ConfigJson = '';

    /**
     * From json get class MTOrder
     * @return array(MTOrder)
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null) return null;
        //---
        return MTOrderJson::GetFromJson($obj);
    }
}

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

?>
