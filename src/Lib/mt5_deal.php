<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class get deals
 */
class MTDealProtocol
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
   * Get dael
   * @param int $ticket - ticket
   * @param MTDeal $deal
   * @return MTRetCode
   */
  public function DealGet($ticket, &$deal)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_TICKET => $ticket);
    //---
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_DEAL_GET, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send deal get failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer deal get is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseDeal(MTProtocolConsts::WEB_CMD_DEAL_GET, $answer, $deal_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse deal get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get object from json
    $deal = $deal_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param string $command command
   * @param  string $answer answer from server
   * @param  MTDealAnswer $deal_answer
   * @return MTRetCode
   */
  private function ParseDeal($command, &$answer, &$deal_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $deal_answer = new MTDealAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $deal_answer->RetCode = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($deal_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if (($deal_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param  string $answer - answer from server
   * @param  MTDealPageAnswer $deal_answer
   * @return MTRetCode
   */
  private function ParseDealPage(&$answer, &$deal_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != MTProtocolConsts::WEB_CMD_DEAL_GET_PAGE) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $deal_answer = new MTDealPageAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $deal_answer->RetCode = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($deal_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if (($deal_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Get total deals for login
   * @param string $login - user login
   * @param int $from - date from
   * @param int $to - date to
   * @param int $total - count of users positions
   * @return MTRetCode
   */
  public function DealGetTotal($login, $from,$to, &$total)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login,MTProtocolConsts::WEB_PARAM_FROM => $from, MTProtocolConsts::WEB_PARAM_TO => $to);
    //---
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_DEAL_GET_TOTAL, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send deal get total failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer deal get total is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseDealTotal($answer, $deal_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse deal get total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get total
    $total = $deal_answer->Total;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Get deals
   * @param int $login - number of ticket
   * @param int $from - from date in unix time
   * @param int $to - to date in unix time
   * @param int $offset - begin records number
   * @param int $total - total records need
   * @param array(MTDeal) $deals
   * @return MTRetCode
   */
  public function DealGetPage($login, $from, $to, $offset, $total, &$deals)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_FROM => $from, MTProtocolConsts::WEB_PARAM_TO => $to, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
    //---
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_DEAL_GET_PAGE, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send deal get page failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer deal get page is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseDealPage($answer, $deal_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse deal get page failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get object from json
    $deals = $deal_answer->GetArrayFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Check answer from MetaTrader 5 server
   * @param  $answer string server answer
   * @param  $deal_answer MTDealTotalAnswer
   * @return false
   */
  private function ParseDealTotal(&$answer, &$deal_answer)
    {
    $pos = 0;
    //--- get command answer
    $command = $this->m_connect->GetCommand($answer, $pos);
    if ($command != MTProtocolConsts::WEB_CMD_DEAL_GET_TOTAL) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $deal_answer = new MTDealTotalAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $deal_answer->RetCode = $param['value'];
          break;
        case MTProtocolConsts::WEB_PARAM_TOTAL:
          $deal_answer->Total = (int)$param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($deal_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }
  }

/**
 * types of transactions
 */
class MTEnDealAction
  {
  const DEAL_BUY                = 0; // buy
  const DEAL_SELL               = 1; // sell
  const DEAL_BALANCE            = 2; // deposit operation
  const DEAL_CREDIT             = 3; // credit operation
  const DEAL_CHARGE             = 4; // additional charges
  const DEAL_CORRECTION         = 5; // correction deals
  const DEAL_BONUS              = 6; // bouns
  const DEAL_COMMISSION         = 7; // commission
  const DEAL_COMMISSION_DAILY   = 8; // daily commission
  const DEAL_COMMISSION_MONTHLY = 9; // monthly commission
  const DEAL_AGENT_DAILY        = 10; // daily agent commission
  const DEAL_AGENT_MONTHLY      = 11; // monthly agent commission
  const DEAL_INTERESTRATE       = 12; // interest rate charges
  const DEAL_BUY_CANCELED       = 13; // canceled buy deal
  const DEAL_SELL_CANCELED      = 14; // canceled sell deal
  const DEAL_DIVIDEND           = 15; // dividend
  const DEAL_DIVIDEND_FRANKED   = 16; // franked dividend
  const DEAL_TAX                = 17; // taxes
  const DEAL_AGENT              = 18; // instant agent commission
  const DEAL_SO_COMPENSATION    = 19; // negative balance compensation after stop-out
  //--- enumeration borders
  const DEAL_FIRST = MTEnDealAction::DEAL_BUY;
  const DEAL_LAST  = MTEnDealAction::DEAL_SO_COMPENSATION;
  }

/**
 * deal entry direction
 */
class MTEnEntryFlags
  {
  const ENTRY_IN     = 0;   // in market
  const ENTRY_OUT    = 1;   // out of market
  const ENTRY_INOUT  = 2;   // reverse
  const ENTRY_OUT_BY = 3;   // closed by  hedged position
  const ENTRY_STATE  = 255; // state record
  //--- enumeration borders
  const ENTRY_FIRST = MTEnEntryFlags::ENTRY_IN;
  const ENTRY_LAST  = MTEnEntryFlags::ENTRY_STATE;
  }
      
/**
 * deal creation reasons
 */
class MTEnDealReason
  {
  const DEAL_REASON_CLIENT           = 0;  // deal placed manually
  const DEAL_REASON_EXPERT           = 1;  // deal placed by expert
  const DEAL_REASON_DEALER           = 2;  // deal placed by dealer
  const DEAL_REASON_SL               = 3;  // deal placed due SL
  const DEAL_REASON_TP               = 4;  // deal placed due TP
  const DEAL_REASON_SO               = 5;  // deal placed due Stop-Out
  const DEAL_REASON_ROLLOVER         = 6;  // deal placed due rollover
  const DEAL_REASON_EXTERNAL_CLIENT  = 7;  // deal placed from the external system by client
  const DEAL_REASON_VMARGIN          = 8;  // deal placed due variation margin
  const DEAL_REASON_GATEWAY          = 9;  // deal placed by gateway
  const DEAL_REASON_SIGNAL           = 10; // deal placed by signal service
  const DEAL_REASON_SETTLEMENT       = 11; // deal placed due settlement
  const DEAL_REASON_TRANSFER         = 12; // deal placed due position transfer
  const DEAL_REASON_SYNC             = 13; // deal placed due position synchronization
  const DEAL_REASON_EXTERNAL_SERVICE = 14; // deal placed from the external system due service issues
  const DEAL_REASON_MIGRATION        = 15; // deal placed due migration
  const DEAL_REASON_MOBILE           = 16; // deal placed manually by mobile terminal
  const DEAL_REASON_WEB              = 17; // deal placed manually by web terminal
  const DEAL_REASON_SPLIT            = 18; // deal placed due split
  //--- enumeration borders
  const DEAL_REASON_FIRST = MTEnDealReason::DEAL_REASON_CLIENT;
  const DEAL_REASON_LAST  = MTEnDealReason::DEAL_REASON_SPLIT;
  }
  
/**
 * modification flags
 */
class MTEnTradeModifyFlags
  {
  const MODIFY_FLAGS_ADMIN          = 1;
  const MODIFY_FLAGS_MANAGER        = 2;
  const MODIFY_FLAGS_POSITION       = 4;
  const MODIFY_FLAGS_RESTORE        = 8;
  const MODIFY_FLAGS_API_ADMIN      = 16;
  const MODIFY_FLAGS_API_MANAGER    = 32;
  const MODIFY_FLAGS_API_SERVER     = 64;
  const MODIFY_FLAGS_API_GATEWAY    = 128;
  const MODIFY_FLAGS_API_SERVER_ADD = 256;
  //--- enumeration borders
  const MODIFY_FLAGS_NONE = 0;
  const MODIFY_FLAGS_ALL  = 511;
  }

/**
 * Deal information
 */
class MTDeal
  {
  /**
   * deal ticket
   * @var int
   */
  public $Deal;
  /**
   * deal ticket in external system (exchange, ECN, etc)
   * @var string
   */
  public $ExternalID;
  /**
   * client login
   * @var int
   */
  public $Login;
  /**
   * processed dealer login (0-means auto)
   * @var int
   */
  public $Dealer;
  /**
   * deal order ticket
   * @var int
   */
  public $Order;
  /**
   * EnDealAction
   * @var EnDealAction
   */
  public $Action;
  /**
   * EnEntryFlags
   * @var EnEntryFlags
   */
  public $Entry;
  /**
   * EnDealReason
   * @var EnDealReason
   */
  public $Reason;
  /**
   * price digits
   * @var int
   */
  public $Digits;
  /**
   * currency digits
   * @var int
   */
  public $DigitsCurrency;
  /**
   * symbol contract size
   * @var double
   */
  public $ContractSize;
  /**
   * deal creation datetime
   * @var int
   */
  public $Time;
  /**
   * deal creation datetime in msc since 1970.01.01
   * @var string
   */
  public $TimeMsc;
  /**
   * deal symbol
   * @var string
   */
  public $Symbol;
  /**
   * deal price
   * @var double
   */
  public $Price;
  /**
   * deal volume
   * @var int
   */
  public $Volume;
  /**
   * deal volume
   * @var int
   */
  public $VolumeExt;
  /**
   * deal profit
   * @var double
   */
  public $Profit;
  /**
   * deal collected swaps
   * @var double
   */
  public $Storage;
  /**
   * deal commission
   * @var double
   */
  public $Commission;
  /**
   * profit conversion rate (from symbol profit currency to deposit currency)
   * @var double
   */
  public $RateProfit;
  /**
   * margin conversion rate (from symbol margin currency to deposit currency)
   * @var double
   */
  public $RateMargin;
  /**
   * expert id (filled by expert advisor)
   * @var int
   */
  public $ExpertID;
  /**
   * position id
   * @var int
   */
  public $PositionID;
  /**
   * deal comment
   * @var string
   */
  public $Comment;
  /**
   * deal profit in symbol's profit currency
   * @var double
   */
  public $ProfitRaw;
  /**
   * closed position  price
   * @var double
   */
  public $PricePosition;
  /**
   * closed volume
   * @var int
   */
  public $VolumeClosed;
  /**
   * closed volume
   * @var int
   */
  public $VolumeClosedExt;
  /**
   * tick value
   * @var double
   */
  public $TickValue;
  /**
   * tick size
   * @var double
   */
  public $TickSize;
  /**
   * flags
   * @var int
   */
  public $Flags;
  /**
   * source gateway name
   * @var string
   */
  public $Gateway;
  /**
   * tick size
   * @var double
   */
  public $PriceGateway;
  /**
   * EnEntryFlags
   * @var EnEntryFlags
   */
  public $ModifyFlags;
}
/**
 * Answer on request deal_get_total
 */
class MTDealTotalAnswer
  {
  public $RetCode = '-1';
  public $Total = 0;
  }
/**
 * get deal page answer
 */
class MTDealPageAnswer
  {
  public $RetCode = '-1';
  public $ConfigJson = '';
  /**
   * From json get class MTDeal
   * @return array(MTDeal)
   */
  public function GetArrayFromJson()
    {
    $objects = MTJson::Decode( $this->ConfigJson);
    if ($objects == null) return null;
    $result = array();
    //---
    foreach ($objects as $obj)
      {
      $info = MTDealJson::GetFromJson($obj);
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
 * get deal page answer
 */
class MTDealAnswer
  {
  public $RetCode = '-1';
  public $ConfigJson = '';
  /**
   * From json get class MTDeal
   * @return array(MTDeal)
   */
  public function GetFromJson()
    {
    $obj =MTJson::Decode( $this->ConfigJson);
    if ($obj == null) return null;
    //---
    return MTDealJson::GetFromJson($obj);
    }
  }

class MTDealJson
  {
  /**
   * Get MTDeal from json object
   * @param object $obj
   * @return MTDeal
   */
  public static function GetFromJson($obj)
    {
    if ($obj == null) return null;
    $info = new MTDeal();
    //---
    $info->Deal = (int)$obj->Deal;
    $info->ExternalID = (string)$obj->ExternalID;
    $info->Login = (int)$obj->Login;
    $info->Dealer = (int)$obj->Dealer;
    $info->Order = (int)$obj->Order;
    $info->Action = (int)$obj->Action;
    $info->Entry = (int)$obj->Entry;
    $info->Reason = (int)$obj->Reason;
    $info->Digits = (int)$obj->Digits;
    $info->DigitsCurrency = (int)$obj->DigitsCurrency;
    $info->ContractSize = (float)$obj->ContractSize;
    $info->Time = (int)$obj->Time;
    $info->TimeMsc = (int)$obj->TimeMsc;
    $info->Symbol = (string)$obj->Symbol;
    $info->Price = (float)$obj->Price;
    $info->Volume = (int)$obj->Volume;
    if (isset($obj->VolumeExt))
      $info->VolumeExt = (int)$obj->VolumeExt;
    else
      $info->VolumeExt = (int)MTUtils::ToNewVolume($info->Volume);
    $info->Profit = (float)$obj->Profit;
    $info->Storage = (float)$obj->Storage;
    $info->Commission = (float)$obj->Commission;
    $info->RateProfit = (float)$obj->RateProfit;
    $info->RateMargin = (float)$obj->RateMargin;
    $info->ExpertID = (int)$obj->ExpertID;
    $info->PositionID = (int)$obj->PositionID;
    $info->Comment = (string)$obj->Comment;
    $info->ProfitRaw = (float)$obj->ProfitRaw;
    $info->PricePosition = (float)$obj->PricePosition;
    if(isset($obj->PriceSL))
      $info->PriceSL = (float)$obj->PriceSL;
    if(isset($obj->PriceTP))
      $info->PriceTP = (float)$obj->PriceTP;
    $info->VolumeClosed = (int)$obj->VolumeClosed;
    if (isset($obj->VolumeClosedExt))
       $info->VolumeClosedExt = (int)$obj->VolumeClosedExt;
    else
       $info->VolumeClosedExt = (int)MTUtils::ToNewVolume($info->VolumeClosed);
    $info->TickValue = (float)$obj->TickValue;
    $info->TickSize = (float)$obj->TickSize;
    $info->Flags = (int)$obj->Flags;
    $info->Gateway = (string)$obj->Gateway;
    $info->PriceGateway = (float)$obj->PriceGateway;
    $info->ModifyFlags = (int)$obj->ModifyFlags;
    //---
    return $info;
    }
  }

?>