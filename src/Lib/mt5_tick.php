<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with tick
 */
class MTTickProtocol
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
   * Get last ticks
   * @param string $symbol - name symbol
   * @param array(MTTick) $ticks
   * @return MTRetCode
   */
  public function TickLast($symbol, &$ticks)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TICK_LAST, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send tick last failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer tick last is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    $tick_answer = null;

    if (($error_code = $this->Parse(MTProtocolConsts::WEB_CMD_TICK_LAST, $answer, $tick_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse tick last failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get object from json
    $ticks = $tick_answer->GetArrayFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param string $command - command
   * @param string $answer - answer from server
   * @param  MTTickAnswer $tick_answer
   * @return MTRetCode
   */
  private function Parse($command, &$answer, &$tick_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $tick_answer = new MTTickAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $tick_answer->RetCode = $param['value'];
          break;
        case MTProtocolConsts::WEB_PARAM_TRANS_ID:
          $tick_answer->TransId = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($tick_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if (($tick_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Get last tickets by symbol and group
   * @param string $symbol
   * @param string $group
   * @param array(MTTick) $ticks
   * @return MTRetCode
   */
  public function TickLastGroup($symbol, $group, &$ticks)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol, MTProtocolConsts::WEB_PARAM_GROUP => $group);
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TICK_LAST_GROUP, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send tick last group failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer tick last group is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    $tick_answer = null;
    //---
    if (($error_code = $this->Parse(MTProtocolConsts::WEB_CMD_TICK_LAST_GROUP, $answer, $tick_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse tick last group failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get object from json
    $ticks = $tick_answer->GetArrayFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Get stat ticks
   * @param string $symbol - name symbol
   * @param array(MTTickStat) $tick_stat
   * @return MTRetCode
   */
  public function TickStat($symbol, &$tick_stat)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TICK_STAT, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send tick last failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer tick last is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    $tick_answer = null;

    if (($error_code = $this->ParseTickStat(MTProtocolConsts::WEB_CMD_TICK_STAT, $answer, $tick_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse tick last failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get object from json
    $tick_stat = $tick_answer->GetArrayFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param string $command - command
   * @param string $answer - answer from server
   * @param  MTTickAnswer $tick_answer
   * @return MTRetCode
   */
  private function ParseTickStat($command, &$answer, &$tick_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $tick_answer = new MTTickStatAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $tick_answer->RetCode = $param['value'];
          break;
        case MTProtocolConsts::WEB_PARAM_TRANS_ID:
          $tick_answer->TransId = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($tick_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if (($tick_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }
  }
/**
 * description of tick
 */
class MTTick
  {
  //--- symbol
  public $Symbol;
  //---
  public $Digits;
  //--- bid price
  public $Bid;
  //--- ask price
  public $Ask;
  //--- last price
  public $Last;
  //--- volume
  public $Volume;
  //--- volume with extended accuracy
  public $VolumeReal;
  }

/**
 * get tick answer
 */
class MTTickAnswer
  {
  public $RetCode = '-1';
  public $TransId = 0;
  public $ConfigJson = '';
  /**
   * From json get class MTTick
   * @return array(MTTick)
   */
  public function GetArrayFromJson()
    {
    $objects = MTJson::Decode($this->ConfigJson);
    if ($objects == null) return null;
    $result = array();
    //---
    foreach ($objects as $obj)
      {
      $info = MTTickJson::GetFromJson($obj);
      //---
      $result[] = $info;
      }
    //---
    $objects = null;
    //---
    return $result;
    }
  }

class MTTickJson
  {
  /**
   * Get MTTick from json object
   * @param object $obj
   * @return MTTick
   */
  public static function GetFromJson($obj)
    {
    if ($obj == null) return null;
    $info = new MTTick();
    //---
    $info->Symbol = (string)$obj->Symbol;
    $info->Digits = (int)$obj->Digits;
    $info->Bid = (float)$obj->Bid;
    $info->Ask = (float)$obj->Ask;
    $info->Last = (float)$obj->Last;
    $info->Volume = (int)$obj->Volume;
    if(isset($obj->VolumeReal))
      $info->VolumeReal = (float)$obj->VolumeReal;
    else
      $info->VolumeReal = (float)$info->Volume;
    //---
    return $info;
    }
  }
 
/**
 * MTEnDirection
 */
class MTEnDirection
  {
  const DIR_NONE = 0; // direction unknown
  const DIR_UP   = 1; // price up
  const DIR_DOWN = 2; // price down
  //--- enumeration borders
  const DIR_FIRST = MTEnDirection::DIR_NONE;
  const DIR_LAST  = MTEnDirection::DIR_DOWN;
  }
  
/**
 * Tick stat
 */
class MTTickStat
  {
  //--- symbol
  public $Symbol;
  //--- digits
  public $Digits;
  //--- bid
  public $Bid;
  public $BidLow;
  public $BidHigh;
  public $BidDir;
  //--- ask
  public $Ask;
  public $AskLow;
  public $AskHigh;
  public $AskDir;
  //--- last price
  public $Last;
  public $LastLow;
  public $LastHigh;
  public $LastDir;
  //--- volume
  public $Volume;
  public $VolumeReal;
  public $VolumeLow;
  public $VolumeLowReal;
  public $VolumeHigh;
  public $VolumeHighReal;
  public $VolumeDir;
  //--- trade
  public $TradeDeals;
  public $TradeVolume;
  public $TradeVolumeReal;
  public $TradeTurnover;
  public $TradeInterest;
  public $TradeBuyOrders;
  public $TradeBuyVolume;
  public $TradeBuyVolumeReal;
  public $TradeSellOrders;
  public $TradeSellVolume;
  public $TradeSellVolumeReal;
  //--- price
  public $PriceOpen;
  public $PriceClose;
  public $PriceChange;
  public $PriceVolatility;
  public $PriceTheoretical;
}
/**
 * get tick answer
 */
class MTTickStatAnswer
  {
  public $RetCode = '-1';
  public $TransId = 0;
  public $ConfigJson = '';
  /**
   * From json get class MTTickStat
   * @return array(MTTickStat)
   */
  public function GetArrayFromJson()
    {
     $objects = MTJson::Decode($this->ConfigJson);
    if ($objects == null) return null;
    $result = array();
    //---
    foreach ($objects as $obj)
      {
      $info = MTTickStatJson::GetFromJson($obj);
      //---
      $result[] = $info;
      }
    //---
    $objects = null;
    //---
    return $result;
    }
  }
class MTTickStatJson
  {
  /**
   * Get MTTickState from json object
   * @param object $obj
   * @return MTTickStat
   */
  public static function GetFromJson($obj)
    {
    if ($obj == null) return null;
    $info = new MTTickStat();
    //---
    $info->Symbol = (string)$obj->Symbol;
    $info->Digits = (int)$obj->Digits;
    $info->Bid = (float)$obj->Bid;
    $info->BidLow = (float)$obj->BidLow;
    $info->BidHigh = (float)$obj->BidHigh;
    $info->BidDir = (int)$obj->BidDir;
    $info->Ask = (float)$obj->Ask;
    $info->AskLow = (float)$obj->AskLow;
    $info->AskHigh = (float)$obj->AskHigh;
    $info->AskDir = (int)$obj->AskDir;
    $info->Last = (float)$obj->Last;
    $info->LastLow = (float)$obj->LastLow;
    $info->LastHigh = (float)$obj->LastHigh;
    $info->LastDir = (int)$obj->LastDir;
    //---
    $info->Volume = (int)$obj->Volume;
    if(isset($obj->VolumeReal))
      $info->VolumeReal = (float)$obj->VolumeReal;
    else
      $info->VolumeReal = (float)$info->Volume;
    //---
    $info->VolumeLow = (int)$obj->VolumeLow;
    if(isset($obj->VolumeLowReal))
       $info->VolumeLowReal = (float)$obj->VolumeLowReal;
    else
       $info->VolumeLowReal = (float)$info->VolumeLow;
    //---
    $info->VolumeHigh = (int)$obj->VolumeHigh;
    if(isset($obj->VolumeHighReal))
      $info->VolumeHighReal = (float)$obj->VolumeHighReal;
    else
      $info->VolumeHighReal = (float)$info->VolumeHigh;
    //---
    $info->VolumeDir = (int)$obj->VolumeDir;
    $info->TradeDeals = (int)$obj->TradeDeals;
    //---
    $info->TradeVolume = (int)$obj->TradeVolume;
    if(isset($obj->TradeVolumeReal))
      $info->TradeVolumeReal = (float)$obj->TradeVolumeReal;
    else
      $info->TradeVolumeReal = (float)$info->TradeVolume;
    //---
    $info->TradeTurnover = (int)$obj->TradeTurnover;
    $info->TradeInterest = (int)$obj->TradeInterest;
    $info->TradeBuyOrders = (int)$obj->TradeBuyOrders;
    //---
    $info->TradeBuyVolume = (int)$obj->TradeBuyVolume;
    if(isset($obj->TradeBuyVolumeReal))
      $info->TradeBuyVolumeReal = (float)$obj->TradeBuyVolumeReal;
    else
      $info->TradeBuyVolumeReal = (float)$info->TradeBuyVolume;
    //---
    $info->TradeSellOrders = (int)$obj->TradeSellOrders;
    //---
    $info->TradeSellVolume = (int)$obj->TradeSellVolume;
    if(isset($obj->TradeSellVolumeReal))
      $info->TradeSellVolumeReal = (float)$obj->TradeSellVolumeReal;
    else
      $info->TradeSellVolumeReal = (float)$info->TradeSellVolume;
    //---
    $info->PriceOpen = (float)$obj->PriceOpen;
    $info->PriceClose = (float)$obj->PriceClose;
    $info->PriceChange = (float)$obj->PriceChange;
    $info->PriceVolatility = (float)$obj->PriceVolatility;
    $info->PriceTheoretical = (float)$obj->PriceTheoretical;
    //---
    return $info;
    }
  }

?>