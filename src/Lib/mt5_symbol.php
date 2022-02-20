<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTSymbolProtocol
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
   * Get total symbols
   *
   * @param int $total - total symbols
   *
   * @return MTRetCode
   */
  public function SymbolTotal(&$total)
    {
    //--- send request
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_TOTAL, null))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send symbol total failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer symbol total is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseSymbolTotal($answer, $group)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse symbol total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //---
    $total = $group->Total;
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Check answer from MetaTrader 5 server
   *
   * @param  $answer        string server answer
   * @param  $symbol_answer MTSymbolTotalAnswer
   *
   * @return false
   */
  private function ParseSymbolTotal(&$answer, &$symbol_answer)
    {
    $pos = 0;
    //--- get command answer
    $command = $this->m_connect->GetCommand($answer, $pos);
    if($command != MTProtocolConsts::WEB_CMD_SYMBOL_TOTAL) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $symbol_answer = new MTSymbolTotalAnswer();
    //--- get param
    $pos_end = -1;
    while(($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
    {
      switch($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $symbol_answer->RetCode = $param['value'];
          break;
        case MTProtocolConsts::WEB_PARAM_TOTAL:
          $symbol_answer->Total = (int)$param['value'];
          break;
      }
    }
    //--- check ret code
    if(($ret_code = MTConnect::GetRetCode($symbol_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Get symbol config
   *
   * @param $pos         int from 0 to total
   * @param $symbol_next MTConSymbol
   *
   * @return MTRetCode
   */
  public function SymbolNext($pos, &$symbol_next)
    {
    $pos = (int)$pos;
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_INDEX => $pos);
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_NEXT, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send symbol next failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer symbol next is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_NEXT, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse symbol next failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //--- get object from json
    $symbol_next = $symbol_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * check answer from MetaTrader 5 server
   *
   * @param  $command       string command
   * @param  $answer        string answer from server
   * @param  $symbol_answer MTSymbolAnswer
   *
   * @return MTRetCode
   */
  private function ParseSymbol($command, &$answer, &$symbol_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $symbol_answer = new MTSymbolAnswer();
    //--- get param
    $pos_end = -1;
    while(($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
    {
      switch($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $symbol_answer->RetCode = $param['value'];
          break;
      }
    }
    //--- check ret code
    if(($ret_code = MTConnect::GetRetCode($symbol_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if(($symbol_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Get symbol config
   *
   * @param $name   string - symbol name
   * @param $symbol MTConSymbol
   *
   * @return MTRetCode
   */
  public function SymbolGet($name, &$symbol)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $name);
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_GET, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send symbol get failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer symbol get is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_GET, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse symbol get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //--- get object from json
    $symbol = $symbol_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Get symbol config
   *
   * @param $name   string - symbol name
   * @param $group  string - group name
   * @param $symbol MTConSymbol
   *
   * @return MTRetCode
   */
  public function SymbolGetGroup($name, $group, &$symbol)
    {
    $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $name,
                  MTProtocolConsts::WEB_PARAM_GROUP  => $group);
    //--- send request
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_GET_GROUP, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send symbol get group failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer symbol get group is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_GET_GROUP, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse symbol get group failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //--- get object from json
    $symbol = $symbol_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Add symbol
   *
   * @param MTConSymbol $symbol
   * @param MTConSymbol $new_symbol
   *
   * @return MTRetCode
   */
  public function SymbolAdd($symbol, &$new_symbol)
    {
    $data = array(MTProtocolConsts::WEB_PARAM_BODYTEXT => $this->GetSymbolParams($symbol));
    //--- send request
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_ADD, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send symbol add failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer symbol add is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_ADD, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse symbol add failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //--- get object from json
    $new_symbol = $symbol_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Delete symbol
   *
   * @param string $name
   *
   * @return MTRetCode
   */
  public function SymbolDelete($name)
    {
    $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $name);
    //--- send request
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_DELETE, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send symbol delete failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer symbol delete is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_SYMBOL_DELETE, $answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse symbol delete failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Check answer from MetaTrader 5 server
   *
   * @param  $command string command
   * @param  $answer  string answer from server
   *
   * @return MTRetCode
   */
  private function ParseClearCommand($command, &$answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $user_answer = new MTSymbolAnswer();
    //--- get param
    $pos_end = -1;
    while(($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
    {
      switch($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $user_answer->RetCode = $param['value'];
          break;
      }
    }
    //--- check ret code
    if(($ret_code = MTConnect::GetRetCode($user_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Get params for send symbol
   *
   * @param MTConSymbol $obj - symbol information
   *
   * @return string - json
   */
  private function GetSymbolParams($obj)
    {
    if(isset($obj->MarginRateInitial))
      $this->GetMarginRateInitialForJson($obj);
    if(isset($obj->MarginRateMaintenance))
      $this->GetMarginRateMaintenanceForJson($obj);
    //---
    unset($obj->MarginRateInitial);
    unset($obj->MarginRateMaintenance);
    //--- re-map to real json name
    if(isset($obj->MarginRateLiquidity))
      $obj->MarginLiquidity = $obj->MarginRateLiquidity;
    if(isset($obj->MarginRateCurrency))
      $obj->MarginCurrency = $obj->MarginRateCurrency;
    //---
    return MTJson::Encode($obj);
    }

  /**
   * array MarginRateInitial for json

   *
*@param MTConSymbol $objSymbol
   */
  private function GetMarginRateInitialForJson(&$objSymbol)
    {
    //--- set data
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialBuy = "default";
    else
      $objSymbol->MarginInitialBuy = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY];
    //---
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialSell = "default";
    else
      $objSymbol->MarginInitialSell = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL];
    //---
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialBuyLimit = "default";
    else
      $objSymbol->MarginInitialBuyLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
//---
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialSellLimit = "default";
    else
      $objSymbol->MarginInitialSellLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
//---
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialBuyStop = "default";
    else
      $objSymbol->MarginInitialBuyStop = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
//---
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialSellStop = "default";
    else
      $objSymbol->MarginInitialSellStop = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
//---
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialBuyStopLimit = "default";
    else
      $objSymbol->MarginInitialBuyStopLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
//---
    if(!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginInitialSellStopLimit = "default";
    else
      $objSymbol->MarginInitialSellStopLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }

  /**
   * array MarginRateInitial for json
   *
   * @param MTConSymbol $objSymbol
   */
  private function GetMarginRateMaintenanceForJson(&$objSymbol)
    {
    //--- set data
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceBuy = "default";
    else
      $objSymbol->MarginMaintenanceBuy = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY];
    //---
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceSell = "default";
    else
      $objSymbol->MarginMaintenanceSell = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL];
    //---
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceBuyLimit = "default";
    else
      $objSymbol->MarginMaintenanceBuyLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
//---
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceSellLimit = "default";
    else
      $objSymbol->MarginMaintenanceSellLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
//---
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceBuyStop = "default";
    else
      $objSymbol->MarginMaintenanceBuyStop = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
//---
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceSellStop = "default";
    else
      $objSymbol->MarginMaintenanceSellStop = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
//---
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceBuyStopLimit = "default";
    else
      $objSymbol->MarginMaintenanceBuyStopLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
//---
    if(!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $objSymbol->MarginMaintenanceSellStopLimit = "default";
    else
      $objSymbol->MarginMaintenanceSellStopLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }
  }

/**
 * Answer on request symbol_total
 */
class MTSymbolTotalAnswer
  {
  public $RetCode = '-1';
  public $Total = 0;
  }

/**
 * get symbol info
 */
class MTSymbolAnswer
  {
  public $RetCode = '-1';
  public $ConfigJson = '';

  /**
   * From json get class MTConSymbol
   * @return MTConSymbol
   */
  public function GetFromJson()
    {
    $obj = MTJson::Decode($this->ConfigJson);
    if($obj == null)
       return null;
    
    $result = new MTConSymbol();
    //---
    $result->Symbol               = (string)$obj->Symbol;
    $result->Path                 = (string)$obj->Path;
    $result->ISIN                 = (string)$obj->ISIN;
    $result->Description          = (string)$obj->Description;
    $result->International        = (string)$obj->International;
    $result->Basis                = (string)$obj->Basis;
    $result->Source               = (string)$obj->Source;
    $result->Page                 = (string)$obj->Page;
    $result->CurrencyBase         = (string)$obj->CurrencyBase;
    $result->CurrencyBaseDigits   = (int)$obj->CurrencyBaseDigits;
    $result->CurrencyProfit       = (string)$obj->CurrencyProfit;
    $result->CurrencyProfitDigits = (int)$obj->CurrencyProfitDigits;
    $result->CurrencyMargin       = (string)$obj->CurrencyMargin;
    $result->CurrencyMarginDigits = (int)$obj->CurrencyMarginDigits;
    $result->Color                = (int)$obj->Color;
    $result->ColorBackground      = (int)$obj->ColorBackground;
    $result->Digits               = (int)$obj->Digits;
    $result->Point                = (float)$obj->Point;
    $result->Multiply             = (float)$obj->Multiply;
    $result->TickFlags            = (int)$obj->TickFlags;
    $result->TickBookDepth        = (int)$obj->TickBookDepth;
    $result->ChartMode            = (int)$obj->TickChartMode;
    $result->FilterSoft           = (int)$obj->FilterSoft;
    $result->FilterSoftTicks      = (int)$obj->FilterSoftTicks;
    $result->FilterHard           = (int)$obj->FilterHard;
    $result->FilterHardTicks      = (int)$obj->FilterHardTicks;
    $result->FilterDiscard        = (int)$obj->FilterDiscard;
    $result->FilterSpreadMax      = (int)$obj->FilterSpreadMax;
    $result->FilterSpreadMin      = (int)$obj->FilterSpreadMin;
    $result->FilterGap            = (int)$obj->FilterGap;
    $result->FilterGapTicks       = (int)$obj->FilterGapTicks;
    $result->TradeMode            = (int)$obj->TradeMode;
    $result->TradeFlags           = (int)$obj->TradeFlags;
    $result->CalcMode             = (int)$obj->CalcMode;
    $result->ExecMode             = (int)$obj->ExecMode;
    $result->GTCMode              = (int)$obj->GTCMode;
    $result->FillFlags            = (int)$obj->FillFlags;
    $result->ExpirFlags           = (int)$obj->ExpirFlags;
    $result->OrderFlags           = (int)$obj->OrderFlags;
    $result->Spread               = (int)$obj->Spread;
    $result->SpreadBalance        = (int)$obj->SpreadBalance;
    $result->SpreadDiff           = (int)$obj->SpreadDiff;
    $result->SpreadDiffBalance    = (int)$obj->SpreadDiffBalance;
    $result->TickValue            = (float)$obj->TickValue;
    $result->TickSize             = (float)$obj->TickSize;
    $result->ContractSize         = (float)$obj->ContractSize;
    $result->StopsLevel           = (int)$obj->StopsLevel;
    $result->FreezeLevel          = (int)$obj->FreezeLevel;
    $result->QuotesTimeout        = (int)$obj->QuotesTimeout;
    $result->VolumeMin            = (int)$obj->VolumeMin;
    if(isset($obj->VolumeMinExt))
      $result->VolumeMinExt       = (int)$obj->VolumeMinExt;
    else
      $result->VolumeMinExt       = MTUtils::ToNewVolume($obj->VolumeMin);
    $result->VolumeMax            = (int)$obj->VolumeMax;
    if(isset($obj->VolumeMaxExt))
      $result->VolumeMaxExt       = (int)$obj->VolumeMaxExt;
    else
      $result->VolumeMaxExt       = MTUtils::ToNewVolume($obj->VolumeMax);
    $result->VolumeStep           = (int)$obj->VolumeStep;
    if(isset($obj->VolumeStepExt))
      $result->VolumeStepExt      = (int)$obj->VolumeStepExt;
    else
      $result->VolumeStepExt      = MTUtils::ToNewVolume($obj->VolumeStep);
    $result->VolumeLimit          = (int)$obj->VolumeLimit;
    if(isset($obj->VolumeLimitExt))
      $result->VolumeLimitExt     = (int)$obj->VolumeLimitExt;
    else
      $result->VolumeLimitExt     = MTUtils::ToNewVolume($obj->VolumeLimit);
    //---
    $result->MarginFlags         = (int)$obj->MarginFlags;
    $result->MarginInitial       = (float)$obj->MarginInitial;
    $result->MarginMaintenance   = (float)$obj->MarginMaintenance;
    $result->MarginRateLiquidity = (float)$obj->MarginLiquidity;
    $result->MarginHedged        = (float)$obj->MarginHedged;
    $result->MarginRateCurrency  = (float)$obj->MarginCurrency;
    //---
    $this->SetMarginRateInitial($result, $obj);
    $result->MarginRateMaintenance = $this->GetMarginRateMaintenance($obj);
    //---
    $result->SwapMode          = (int)$obj->SwapMode;
    $result->SwapLong          = (float)$obj->SwapLong;
    $result->SwapShort         = (float)$obj->SwapShort;
    $result->Swap3Day          = (int)$obj->Swap3Day;
    $result->TimeStart         = (int)$obj->TimeStart;
    $result->TimeExpiration    = (int)$obj->TimeExpiration;
    //--- data of session
    $result->SessionsQuotes = $this->GetSessions($obj->SessionsQuotes);
    $result->SessionsTrades = $this->GetSessions($obj->SessionsTrades);
    //---
    $result->REFlags      = (int)$obj->REFlags;
    $result->RETimeout    = (int)$obj->RETimeout;
    $result->IECheckMode  = (int)$obj->IECheckMode;
    $result->IETimeout    = (int)$obj->IETimeout;
    $result->IESlipProfit = (int)$obj->IESlipProfit;
    $result->IESlipLosing = (int)$obj->IESlipLosing;
    $result->IEVolumeMax  = (int)$obj->IEVolumeMax;
    if(isset($obj->IEVolumeMaxExt))
      $result->IEVolumeMaxExt = (int)$obj->IEVolumeMaxExt;
    else
      $result->IEVolumeMaxExt = MTUtils::ToNewVolume($obj->IEVolumeMax);
    //---
    $result->PriceSettle   = (float)$obj->PriceSettle;
    $result->PriceLimitMax = (float)$obj->PriceLimitMax;
    $result->PriceLimitMin = (float)$obj->PriceLimitMin;
    $result->PriceStrike   = (float)$obj->PriceStrike;
    //--- support both name old or new
    if(isset($obj->OptionMode))
       $result->OptionsMode = (int)$obj->OptionMode;
    else if(isset($obj->OptionsMode))
       $result->OptionsMode = (int)$obj->OptionsMode;
    //---
    $result->FaceValue       = (float)$obj->FaceValue;
    $result->AccruedInterest = (float)$obj->AccruedInterest;
    $result->SpliceType      = (int)$obj->SpliceType;
    $result->SpliceTimeType  = (int)$obj->SpliceTimeType;
    $result->SpliceTimeDays  = (int)$obj->SpliceTimeDays;
    //---
    $obj = null;
    //---
    return $result;
    }

  /**
   * get data for MarginRateInitial
   *
   * @param $obj
   *
   * @return array
   */
  private function SetMarginRateInitial(&$symbol, $obj)
    {
     $result = MTConSymbol::GetDefaultMarginRate();
     $new    = false;
    
     if(isset($obj->MarginInitialBuy))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $obj->MarginInitialBuy;
        $new = true;
       }
       
     if(isset($obj->MarginInitialSell))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $obj->MarginInitialSell;
        $new = true;
       }
       
     if(isset($obj->MarginInitialBuyLimit))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] = $obj->MarginInitialBuyLimit;
        $new = true;
       }
       
     if(isset($obj->MarginInitialSellLimit))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $obj->MarginInitialSellLimit;
        $new = true;
       }
       
     if(isset($obj->MarginInitialBuyStop))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] = $obj->MarginInitialBuyStop;
        $new = true;
       }
       
     if(isset($obj->MarginInitialSellStop))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $obj->MarginInitialSellStop;
        $new = true;
       }
       
     if(isset($obj->MarginInitialBuyStopLimit))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] = $obj->MarginInitialBuyStopLimit;
        $new = true;
       }
       
     if(isset($obj->MarginInitialSellStopLimit))
       {
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $obj->MarginInitialSellStopLimit;
        $new = true;
       }
     
     if(!$new)
        $this->OldMarginRateInitialConvert($symbol, $obj);
     else
       {
        $symbol->MarginRateInitial = $result;
        $this->OldMarginRateInitialSet($symbol, $obj);
       } 
    }

  /**
   * convert from deprecated values to actual 
   */
  private function OldMarginRateInitialConvert(&$symbol, $obj)
    {
     $result    = MTConSymbol::GetDefaultMarginRate();
     $has_limit = false;

     if(isset($obj->MarginLong))
       {
        $symbol->MarginLong = (float)$obj->MarginLong;
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $symbol->MarginLong;
       }

     if(isset($obj->MarginShort))
       {
        $symbol->MarginShort = (float)$obj->MarginShort;
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $symbol->MarginShort;
       }

     if(isset($obj->MarginLimit))
       {
        $symbol->MarginLimit = (float)$obj->MarginLimit;
        
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]  = $symbol->MarginLimit * $symbol->MarginLong;
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $symbol->MarginLimit * $symbol->MarginShort;
       }

     if(isset($obj->MarginStop))
       {
        $symbol->MarginStop = (float)$obj->MarginStop;
        
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]  = $symbol->MarginStop * $symbol->MarginLong;
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $symbol->MarginStop * $symbol->MarginShort;
       }

     if(isset($obj->MarginStopLimit))
       {
        $symbol->MarginStopLimit = (float)$obj->MarginStopLimit;
        
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]  = $symbol->MarginStopLimit * $symbol->MarginLong;
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $symbol->MarginStopLimit * $symbol->MarginShort;
       }
       
     $symbol->MarginRateInitial = $result; 
    }

  /**
   * set deprecated values for compatibility 
   */
   private function OldMarginRateInitialSet(&$symbol, $obj)
     {
      $symbol->MarginLong  = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY];
      $symbol->MarginShort = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL];

      $marginLimitLong     = 0; 
      $marginStopLong      = 0; 
      $marginStopLimitLong = 0; 

      $marginLimitShort     = 0; 
      $marginStopShort      = 0; 
      $marginStopLimitShort = 0; 

      if($symbol->MarginLong!=0)
        {
         $marginLimitLong     = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]      / $symbol->MarginLong;
         $marginStopLong      = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]       / $symbol->MarginLong;
         $marginStopLimitLong = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] / $symbol->MarginLong;
        }

      if($symbol->MarginShort!=0)
        {
         $marginLimitShort     = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]      / $symbol->MarginShort;
         $marginStopShort      = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]       / $symbol->MarginShort;
         $marginStopLimitShort = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] / $symbol->MarginShort;
        }

      $symbol->MarginLimit     = max($marginLimitLong,     $marginLimitShort);
      $symbol->MarginStop      = max($marginStopLong,      $marginStopShort);
      $symbol->MarginStopLimit = max($marginStopLimitLong, $marginStopLimitShort);
     }

  /**
   * get data for MarginRateMaintenance
   *
   * @param $obj
   *
   * @return array
   */
  private function GetMarginRateMaintenance($obj)
    {
     $result = MTConSymbol::GetDefaultMarginRate();
     //--- set data
     if(isset($obj->MarginMaintenanceBuy))
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $obj->MarginMaintenanceBuy;
     if(isset($obj->MarginMaintenanceSell))
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $obj->MarginMaintenanceSell;
     if(isset($obj->MarginMaintenanceBuyLimit))
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] = $obj->MarginMaintenanceBuyLimit;
     if(isset($obj->MarginMaintenanceSellLimit))
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $obj->MarginMaintenanceSellLimit;
     if(isset($obj->MarginMaintenanceBuyStop))
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] = $obj->MarginMaintenanceBuyStop;
     if(isset($obj->MarginMaintenanceSellStop))
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $obj->MarginMaintenanceSellStop;
     if(isset($obj->MarginMaintenanceBuyStopLimit))
        $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] = $obj->MarginMaintenanceBuyStopLimit;
     if(isset($obj->MarginMaintenanceSellStopLimit))
        $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $obj->MarginMaintenanceSellStopLimit;
     //---
     return $result;
    }

  /**
   * @param $list array - info about session
   *
   * @return array|null
   */
  private function GetSessions($list)
    {
    if(empty($list)) return null;
    $result = array();
    //---
    $i = 0;
    foreach($list as $sessions)
    {
      if(empty($sessions) || empty($sessions[0]))
      {
        $result[$i] = null;
        $i++;
        continue;
      }
      //---
      $result[$i] = array();
      //---
      foreach($sessions as $session)
      {
        //---
        $sess        = new MTConSymbolSession();
        $sess->Open  = $session->Open;
        $sess->Close = $session->Close;
        //---
        $result[$i][] = $sess;
      }
      //---
      $i++;
    }
    return $result;
    }
  }

/**
 * Symbol trade and quotes sessions config
 */
class MTConSymbolSession
  {
  public $Open;
  public $Close;
  }

/**
 * allowed filling modes flags
 */
class MTEnFillingFlags
  {
  const FILL_FLAGS_NONE = 0; // none
  const FILL_FLAGS_FOK  = 1; // allowed FOK
  const FILL_FLAGS_IOC  = 2; // allowed IOC
  //--- flags borders
  const FILL_FLAGS_FIRST = MTEnFillingFlags::FILL_FLAGS_FOK;
  const FILL_FLAGS_ALL   = 3; //MTEnFillingFlags::FILL_FLAGS_FOK | MTEnFillingFlags::FILL_FLAGS_IOC;
  }
  
/**
 * allowed order expiration modes flags
 */
class  MTEnExpirationFlags
  {
  const TIME_FLAGS_NONE          = 0; // none
  const TIME_FLAGS_GTC           = 1; // allowed Good Till Cancel
  const TIME_FLAGS_DAY           = 2; // allowed Good Till Day
  const TIME_FLAGS_SPECIFIED     = 4; // allowed specified expiration date
  const TIME_FLAGS_SPECIFIED_DAY = 8; // allowed specified expiration date as day
  //--- flags borders
  const TIME_FLAGS_FIRST = MTEnExpirationFlags::TIME_FLAGS_GTC;
  const TIME_FLAGS_ALL   = 15; // TIME_FLAGS_GTC|TIME_FLAGS_DAY|TIME_FLAGS_SPECIFIED|TIME_FLAGS_SPECIFIED_DAY
  }
  
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
  
  /**
   * allowed trade modes
   */
class  MTEnTradeMode
  {
  const TRADE_DISABLED  = 0; // trade disabled
  const TRADE_LONGONLY  = 1; // only long positions allowed
  const TRADE_SHORTONLY = 2; // only short positions allowed
  const TRADE_CLOSEONLY = 3; // only positions closure
  const TRADE_FULL      = 4; // all trade operations are allowed
  //--- enumeration borders
  const TRADE_FIRST = MTEnTradeMode::TRADE_DISABLED;
  const TRADE_LAST  = MTEnTradeMode::TRADE_FULL;
  
  /**
   * Get object
   *
   * @param $id
   *
   * @return MTEnTradeMode
   */
  public static function Get($id)
    {
    $id = (int)$id;
    switch($id)
      {
      case MTEnTradeMode::TRADE_DISABLED:
          return MTEnTradeMode::TRADE_DISABLED;
          //---
      case MTEnTradeMode::TRADE_LONGONLY:
          return MTEnTradeMode::TRADE_LONGONLY;
          //---
      case MTEnTradeMode::TRADE_SHORTONLY:
          return MTEnTradeMode::TRADE_SHORTONLY;
          //---
      case MTEnTradeMode::TRADE_CLOSEONLY:
          return MTEnTradeMode::TRADE_CLOSEONLY;
          //---
      case MTEnTradeMode::TRADE_FULL:
          return MTEnTradeMode::TRADE_FULL;
          //---
      case MTEnTradeMode::TRADE_FIRST:
          return MTEnTradeMode::TRADE_FIRST;
          //---
      case MTEnTradeMode::TRADE_LAST:
          return MTEnTradeMode::TRADE_LAST;
      }
    }
  }
  
/**
 * order execution modes
 */
class  MTEnExecutionMode
  {
  const EXECUTION_REQUEST  = 0; // Request Execution
  const EXECUTION_INSTANT  = 1; // Instant Execution
  const EXECUTION_MARKET   = 2; // Market Execution
  const EXECUTION_EXCHANGE = 3; // Exchange Execution
  //--- enumeration borders
  const EXECUTION_FIRST = MTEnExecutionMode::EXECUTION_REQUEST;
  const EXECUTION_LAST  = MTEnExecutionMode::EXECUTION_EXCHANGE;
  }
  
/**
 * profit and margin calculation modes
 */
class  MTEnCalcMode
  {
  //--- market maker modes
  const TRADE_MODE_FOREX       = 0;
  const TRADE_MODE_FUTURES     = 1;
  const TRADE_MODE_CFD         = 2;
  const TRADE_MODE_CFDINDEX    = 3;
  const TRADE_MODE_CFDLEVERAGE = 4;
  const TRADEMODE_FOREX_NO_LEVERAGE   = 5;
  //--- market makers enumerations
  const TRADE_MODE_MM_FIRST = MTEnCalcMode::TRADE_MODE_FOREX;
  const TRADE_MODE_MM_LAST  = MTEnCalcMode::TRADEMODE_FOREX_NO_LEVERAGE;
  //--- exchange modes
  const TRADE_MODE_EXCH_STOCKS        = 32;
  const TRADE_MODE_EXCH_FUTURES       = 33;
  const TRADE_MODE_EXCH_FUTURES_FORTS = 34;
  const TRADE_MODE_EXCH_OPTIONS       = 35;
  const TRADE_MODE_EXCH_OPTIONS_MARGIN= 36;
  const TRADE_MODE_EXCH_BONDS         = 37;
  const TRADE_MODE_EXCH_STOCKS_MOEX   = 38;
  const TRADE_MODE_EXCH_BONDS_MOEX    = 39;
  //--- exchange enumerations
  const TRADE_MODE_EXCH_FIRST = MTEnCalcMode::TRADE_MODE_EXCH_STOCKS;
  const TRADE_MODE_EXCH_LAST  = MTEnCalcMode::TRADE_MODE_EXCH_BONDS_MOEX;
  //--- service modes
  const TRADE_MODE_SERV_COLLATERAL    =64;
  //--- service enumerations
  const TRADE_MODE_SERV_FIRST =MTEnCalcMode::TRADE_MODE_SERV_COLLATERAL;
  const TRADE_MODE_SERV_LAST  =MTEnCalcMode::TRADE_MODE_SERV_COLLATERAL;
  //--- enumeration borders
  const TRADE_MODE_FIRST = MTEnCalcMode::TRADE_MODE_FOREX;
  const TRADE_MODE_LAST  = MTEnCalcMode::TRADE_MODE_SERV_COLLATERAL;
  }
  
/**
 * orders expiration modes
 */
class  MTEnGTCMode
  {
  const ORDERS_GTC            = 0;
  const ORDERS_DAILY          = 1;
  const ORDERS_DAILY_NO_STOPS = 2;
  //--- enumeration borders
  const ORDERS_FIRST = MTEnGTCMode::ORDERS_GTC;
  const ORDERS_LAST  = MTEnGTCMode::ORDERS_DAILY_NO_STOPS;
  }
  
/**
 * tick collection flags
 */
class  MTEnTickFlags
  {
  const TICK_REALTIME   = 1; // allow realtime tick apply
  const TICK_COLLECTRAW = 2; // allow to collect raw ticks
  const TICK_FEED_STATS = 4;  // allow to receive price statisticks from datafeeds
  //--- flags borders
  const TICK_NONE = 0;
  const TICK_ALL  = 7; // TICK_REALTIME | TICK_COLLECTRAW | TICK_FEED_STATS
  }
  
/**
 * chart mode
 */
class MTEnChartMode
  {
  const CHART_MODE_BID_PRICE  = 0;
  const CHART_MODE_LAST_PRICE = 1;
  const CHART_MODE_OLD        = 255;
  //--- enumeration borders
  const CHART_MODE_FIRST = MTEnChartMode::CHART_MODE_BID_PRICE;
  const CHART_MODE_LAST  = MTEnChartMode::CHART_MODE_OLD;
  }
  
/**
 * margin check modes
 */
class  MTEnMarginFlags
  {
  const MARGIN_FLAGS_NONE            = 0; // none
  const MARGIN_FLAGS_CHECK_PROCESS   = 1; // check margin after dealer confirmation
  const MARGIN_FLAGS_CHECK_SLTP      = 2; // check margin on SL-TP trigger
  const MARGIN_FLAGS_HEDGE_LARGE_LEG = 4;  // check margin for hedged positions using large leg
  //--- enumeration borders
  const MARGIN_FLAGS_FIRST = MTEnMarginFlags::MARGIN_FLAGS_NONE;
  const MARGIN_FLAGS_LAST  = MTEnMarginFlags::MARGIN_FLAGS_HEDGE_LARGE_LEG;
  }
  
/**
 * swaps calculation modes
 */
class MTEnSwapMode
  {
  const SWAP_DISABLED              = 0;
  const SWAP_BY_POINTS             = 1;
  const SWAP_BY_SYMBOL_CURRENCY    = 2;
  const SWAP_BY_MARGIN_CURRENCY    = 3;
  const SWAP_BY_GROUP_CURRENCY     = 4;
  const SWAP_BY_INTEREST_CURRENT   = 5;
  const SWAP_BY_INTEREST_OPEN      = 6;
  const SWAP_REOPEN_BY_CLOSE_PRICE = 7;
  const SWAP_REOPEN_BY_BID         = 8;
  const SWAP_BY_PROFIT_CURRENCY    = 9;
  //--- enumeration borders
  const SWAP_FIRST = MTEnSwapMode::SWAP_DISABLED;
  const SWAP_LAST  = MTEnSwapMode::SWAP_BY_PROFIT_CURRENCY;
  }
  
/**
 * Instant Execution Modes
 */
class  MTEnInstantMode
  {
  const INSTANT_CHECK_NORMAL = 0;
  //--- begin and end of check
  const INSTANT_CHECK_FIRST = MTEnInstantMode::INSTANT_CHECK_NORMAL;
  const INSTANT_CHECK_LAST  = MTEnInstantMode::INSTANT_CHECK_NORMAL;
  }
  
/**
 * Request Execution Flags
 */
class MTEnRequestFlags
  {
  const REQUEST_FLAGS_NONE  = 0; // node
  const REQUEST_FLAGS_ORDER = 1; // trade orders should be additional confirmed after quotation
  //--- flags borders
  const REQUEST_FLAGS_ALL = MTEnRequestFlags::REQUEST_FLAGS_ORDER;
  }
  
/**
 * common trade flags
 */
class MTEnTradeFlags
  {
  const TRADE_FLAGS_NONE             = 0; // none
  const TRADE_FLAGS_PROFIT_BY_MARKET = 1; // convert fx profit using market prices
  const TRADE_FLAGS_ALLOW_SIGNALS    = 2; // allow trade signals for symbol
  //--- flags borders
  const TRADE_FLAGS_ALL     = 3; // TRADE_FLAGS_PROFIT_BY_MARKET | TRADE_FLAGS_ALLOW_SIGNALS
  const TRADE_FLAGS_DEFAULT = MTEnTradeFlags::TRADE_FLAGS_ALLOW_SIGNALS;
  }
  
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
  
/**
 * Options Mode
 * Class MTEnOptionMode
 */
class MTEnOptionMode
  {
  const OPTION_MODE_EUROPEAN_CALL = 0;
  const OPTION_MODE_EUROPEAN_PUT  = 1;
  const OPTION_MODE_AMERICAN_CALL = 2;
  const OPTION_MODE_AMERICAN_PUT  = 3;
  //--- enumeration borders
  const OPTION_MODE_FIRST = MTEnOptionMode::OPTION_MODE_EUROPEAN_CALL;
  const OPTION_MODE_LAST  = MTEnOptionMode::OPTION_MODE_AMERICAN_PUT;
  }
  
/**
 * Splice Type
 * Class MTEnSpliceType
 */
class MTEnSpliceType
  {
  const SPLICE_NONE       = 0;
  const SPLICE_UNADJUSTED = 1;
  const SPLICE_ADJUSTED   = 2;
  //--- enumeration borders
  const SPLICE_FIRST = MTEnSpliceType::SPLICE_NONE;
  const SPLICE_LAST  = MTEnSpliceType::SPLICE_ADJUSTED;
  }
  
/**
 * Splice Time Type
 * Class MTEnSpliceTimeType
 */
class MTEnSpliceTimeType
  {
  const SPLICE_TIME_EXPIRATION = 0;
  //--- enumeration borders
  const SPLICE_TIME_FIRST = MTEnSpliceTimeType::SPLICE_TIME_EXPIRATION;
  const SPLICE_TIME_LAST  = MTEnSpliceTimeType::SPLICE_TIME_EXPIRATION;
  }

/**
 * class config symbol
 */
class MTConSymbol
  {
  /**
   * name
   * @var string
   */
  public $Symbol;
  /**
   * hierarchical symbol path (including symbol name)
   * @var string
   */
  public $Path;
  /**
   * ISIN
   * @var string
   */
  public $ISIN;
  /**
   * local description
   * @var string
   */
  public $Description;
  /**
   * internation description
   * @var string
   */
  public $International;
  /**
   * basic symbol name
   * @var string
   */
  public $Basis;
  /**
   * source symbol name
   * @var string
   */
  public $Source;
  /**
   * symbol specification page URL
   * @var string
   */
  public $Page;
  /**
   * symbol base currency
   * @var string
   */
  public $CurrencyBase;
  /**
   * symbol base currency digits
   * @var int
   */
  public $CurrencyBaseDigits;
  /**
   * symbol profit currency
   * @var string
   */
  public $CurrencyProfit;
  /**
   * symbol profit currency digits
   * @var int
   */
  public $CurrencyProfitDigits;
  /**
   * symbol margin currency
   * @var string
   */
  public $CurrencyMargin;
  /**
   * symbol margin currency digits
   * @var int
   */
  public $CurrencyMarginDigits;
  /**
   * symbol color
   * @var int
   */
  public $Color;
  /**
   * symbol background color
   * @var int
   */
  public $ColorBackground;
  /**
   * symbol digits
   * @var int
   */
  public $Digits;
  /**
   * symbol digits derivation (1/10^digits & 10^digits)
   * @var double
   */
  public $Point;
  /**
   * Multiply
   * @var double
   */
  public $Multiply;
  /**
   * MTEnTickFlags
   * @var MTEnTickFlags
   */
  public $TickFlags;
  /**
   * Depth of Market depth (both legs)
   * @var int
   */
  public $TickBookDepth;
  /**
   * chart mode
   * @var MTEnChartMode
   */
  public $ChartMode;
  /**
   * filtration soft level
   * @var int
   */
  public $FilterSoft;
  /**
   * filtration soft level counter
   * @var int
   */
  public $FilterSoftTicks;
  /**
   * filtration hard level
   * @var int
   */
  public $FilterHard;
  /**
   * filtration hard level counter
   * @var int
   */
  public $FilterHardTicks;
  /**
   * filtration discard level
   * @var int
   */
  public $FilterDiscard;
  /**
   * spread max value
   * @var int
   */
  public $FilterSpreadMax;
  /**
   * spread min value
   * @var int
   */
  public $FilterSpreadMin;
  /**
   * gap level
   * @var int
   */
  public $FilterGap;
  /**
   * gap level ticks
   * @var int
   */
  public $FilterGapTicks;
  /**
   * @var MTEnTradeMode
   */
  public $TradeMode;
  /**
   * @var MTEnTradeFlags
   */
  public $TradeFlags;
  /**
   * @var MTEnCalcMode
   */
  public $CalcMode;
  /**
   * @var MTEnExecutionMode
   */
  public $ExecMode;
  /**
   * @var MTEnGTCMode
   */
  public $GTCMode;
  /**
   * @var MTEnFillingFlags
   */
  public $FillFlags;
  /**
   * @var MTEnExpirationFlags
   */
  public $ExpirFlags;
  /**
   * @var MTEnOrderFlags
   */
  public $OrderFlags;
  /**
   * symbol spread (0-floating)
   * @var int
   */
  public $Spread;
  /**
   * spread balance
   * @var int
   */
  public $SpreadBalance;
  /**
   * spread difference
   * @var int
   */
  public $SpreadDiff;
  /**
   * spread difference balance
   * @var int
   */
  public $SpreadDiffBalance;
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
   * contract size
   * @var double
   */
  public $ContractSize;
  /**
   * stops level
   * @var int
   */
  public $StopsLevel;
  /**
   * freeze level
   * @var int
   */
  public $FreezeLevel;
  /**
   * quotes timeout
   * @var int
   */
  public $QuotesTimeout;
  /**
   * minimal volume
   * @var int
   */
  public $VolumeMin;
  /**
   * minimal volume
   * @var int
   */
  public $VolumeMinExt;
  /**
   * maximal volume
   * @var int
   */
  public $VolumeMax;
  /**
   * maximal volume
   * @var int
   */
  public $VolumeMaxExt;
  /**
   * volume step
   * @var int
   */
  public $VolumeStep;
  /**
   * volume step
   * @var int
   */
  public $VolumeStepExt;
  /**
   * cumulative positions and orders limit
   * @var int
   */
  public $VolumeLimit;
  /**
   * cumulative positions and orders limit
   * @var int
   */
  public $VolumeLimitExt;
  /**
   * @var MTEnMarginFlags
   */
  public $MarginFlags;
  /**
   * initial margin
   * @var double
   */
  public $MarginInitial;
  /**
   * maintenance margin
   * @var double
   */
  public $MarginMaintenance;
  /**
   * orders and positions margin rates
   * @var array
   */
  public $MarginRateInitial;
  /**
   * orders and positions margin rates
   * @var array
   */
  public $MarginRateMaintenance;
  /**
   * orders and positions margin rates
   * @var double
   */
  public $MarginRateLiquidity;
  /**
   * hedged positions margin rate
   * @var double
   */
  public $MarginHedged;
  /**
   * margin currency rate
   * @var double
   */
  public $MarginRateCurrency;
  /**
   * long orders and positions margin rate
   * @deprecated should use MarginRateInitial and MarginRateMaintenance 
   * @var double
   */
  public $MarginLong;
  /**
   * short orders and positions margin rate
   * @deprecated should use MarginRateInitial and MarginRateMaintenance
   * @var double
   */
  public $MarginShort;
  /**
   * limit orders and positions margin rate
   * @deprecated should use MarginRateInitial and MarginRateMaintenance
   * @var double
   */
  public $MarginLimit;
  /**
   * stop orders and positions margin rate
   * @deprecated should use MarginRateInitial and MarginRateMaintenance
   * @var double
   */
  public $MarginStop;
  /**
   * stop-limit orders and positions margin rate
   * @deprecated should use MarginRateInitial and MarginRateMaintenance
   * @var double
   */
  public $MarginStopLimit;
  /**
   * @deprecated should use MarginRateInitial and MarginRateMaintenance
   * @var MTEnSwapMode
   */
  public $SwapMode;
  /**
   * long positions swaps rate
   * @var double
   */
  public $SwapLong;
  /**
   * short positions swaps rate
   * @var double
   */
  public $SwapShort;
  /**
   * 3 time swaps day
   * @var int
   */
  public $Swap3Day;
  /**
   * trade start date
   * @var int
   */
  public $TimeStart;
  /**
   * trade end date
   * @var int
   */
  public $TimeExpiration;
  /**
   * quote sessions
   * @var array
   */
  public $SessionsQuotes;
  /**
   * trade sessions
   * @var array
   */
  public $SessionsTrades;
  /**
   * request execution flags
   * @var MTEnRequestFlags
   */
  public $REFlags;
  /**
   * request execution timeout
   * @var int
   */
  public $RETimeout;
  /**
   * instant execution check mode MTEnInstantMode
   * @var MTEnInstantMode
   */
  public $IECheckMode;
  /**
   * instant execution timeout
   * @var int
   */
  public $IETimeout;
  /**
   * instant execution profit slippage
   * @var int
   */
  public $IESlipProfit;
  /**
   * instant execution losing slippage
   * @var int
   */
  public $IESlipLosing;
  /**
   * instant execution max volume
   * @var int
   */
  public $IEVolumeMax;
  /**
   * instant execution max volume
   * @var int
   */
  public $IEVolumeMaxExt;
  /**
   * settle price (for futures)
   * @var double
   */
  public $PriceSettle;
  /**
   * price limit max (for futures)
   * @var double
   */
  public $PriceLimitMax;
  /**
   * price limit min (for futures)
   * @var double
   */
  public $PriceLimitMin;
  /**
   * option strike price value
   * @var double
   */
  public $PriceStrike;
  /**
   * @var MTEnOptionMode
   */
  public $OptionsMode;
  /**
   * @var double
   */
  public $FaceValue;
  /**
   * @var double
   */
  public $AccruedInterest;
  /**
   * @var MTEnSpliceType
   */
  public $SpliceType;
  /**
   * @var MTEnSpliceTimeType
   */
  public $SpliceTimeType;
  /**
   * @var int
   */
  public $SpliceTimeDays;

  /**
   * Create MTConSymbol with default values
   * @return MTConSymbol
   */
  public static function CreateDefault()
    {
    $symbol = new MTConSymbol();
    //---
    $symbol->CurrencyBase          = "USD";
    $symbol->CurrencyProfit        = "USD";
    $symbol->CurrencyMargin        = "USD";
    $symbol->Digits                = 4;
    $symbol->TickBookDepth         = 0;
    $symbol->TickFlags             = MTEnTickFlags::TICK_REALTIME;
    $symbol->FilterDiscard         = 500;
    $symbol->FilterSoftTicks       = 10;
    $symbol->FilterHardTicks       = 10;
    $symbol->FilterHard            = 500;
    $symbol->FilterSoft            = 100;
    $symbol->FilterSpreadMax       = 0;
    $symbol->FilterSpreadMin       = 0;
    $symbol->TradeMode             = MTEnTradeMode::TRADE_FULL;
    $symbol->TradeFlags            = MTEnTradeFlags::TRADE_FLAGS_DEFAULT;
    $symbol->Spread                = 0;
    $symbol->SpreadBalance         = 0;
    $symbol->TickValue             = 0;
    $symbol->TickSize              = 0;
    $symbol->ContractSize          = 100000;
    $symbol->GTCMode               = MTEnGTCMode::ORDERS_GTC;
    $symbol->CalcMode              = MTEnCalcMode::TRADE_MODE_FOREX;
    $symbol->QuotesTimeout         = 0;
    $symbol->PriceSettle           = 0;
    $symbol->PriceLimitMax         = 0;
    $symbol->PriceLimitMin         = 0;
    $symbol->TimeStart             = 0;
    $symbol->TimeExpiration        = 0;
    $symbol->SpreadDiff            = 0;
    $symbol->SpreadDiffBalance     = 0;
    $symbol->StopsLevel            = 5;
    $symbol->FreezeLevel           = 0;
    $symbol->ExecMode              = MTEnExecutionMode::EXECUTION_INSTANT;
    $symbol->FillFlags             = MTEnFillingFlags::FILL_FLAGS_FOK;
    $symbol->ExpirFlags            = MTEnExpirationFlags::TIME_FLAGS_ALL;
    $symbol->REFlags               = MTEnRequestFlags::REQUEST_FLAGS_NONE;
    $symbol->RETimeout             = 7;
    $symbol->IETimeout             = 7;
    $symbol->IESlipProfit          = 2;
    $symbol->IESlipLosing          = 2;
    $symbol->IEVolumeMax           = 0;
    $symbol->IECheckMode           = MTEnInstantMode::INSTANT_CHECK_NORMAL;
    $symbol->VolumeMin             = 0;
    $symbol->VolumeMax             = 100000;
    $symbol->VolumeMaxExt          = MTUtils::ToNewVolume($symbol->VolumeMax);
    $symbol->VolumeStep            = 10000;
    $symbol->VolumeStepExt         = MTUtils::ToNewVolume($symbol->VolumeStep);
    $symbol->VolumeLimit           = 0;
    $symbol->MarginFlags           = MTEnMarginFlags::MARGIN_FLAGS_NONE;
    $symbol->MarginInitial         = 0;
    $symbol->MarginMaintenance     = 0;
    $symbol->MarginRateInitial     = self::GetDefaultMarginRate();
    $symbol->MarginRateMaintenance = self::GetDefaultMarginRate();
    $symbol->MarginRateLiquidity   = 0;
    $symbol->MarginHedged          = 0;
    $symbol->MarginRateCurrency    = 0;
    //--- DEPRECATED
    $symbol->MarginLong            = 1;
    $symbol->MarginShort           = 1;
    $symbol->MarginLimit           = 0;
    $symbol->MarginStop            = 0;
    $symbol->MarginStopLimit       = 0;
    //---
    $symbol->SwapMode              = MTEnSwapMode::SWAP_DISABLED;
    $symbol->SwapLong              = 0;
    $symbol->SwapShort             = 0;
    $symbol->Swap3Day              = 3;
    $symbol->OrderFlags            = MTEnOrderFlags::ORDER_FLAGS_ALL;
    $symbol->OptionsMode           = MTEnOptionMode::OPTION_MODE_EUROPEAN_CALL;
    $symbol->PriceStrike           = 0;
    //---
    $symbol->FaceValue             = 0;
    $symbol->AccruedInterest       = 0;
    $symbol->SpliceType            = MTEnSpliceType::SPLICE_NONE;
    $symbol->SpliceTimeType        = MTEnSpliceTimeType::SPLICE_TIME_EXPIRATION;
    $symbol->SpliceTimeDays        = 0;
    //---
    return $symbol;
    }

  /**
   * Get dafault Margin rate
   * @return array
   */
  public static function GetDefaultMarginRate()
    {
    return array(MTEnMarginRateTypes::MARGIN_RATE_BUY             => 0.0,
                 MTEnMarginRateTypes::MARGIN_RATE_SELL            => 0.0,
                 MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT       => 0.0,
                 MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT      => 0.0,
                 MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP        => 0.0,
                 MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP       => 0.0,
                 MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT  => 0.0,
                 MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT => 0.0);
    }
  }

/**
 * hedging flags
 */
class MTEnHedgeFlags
  {
  const HEDGE_FLAGS_NONE          =0; // all disabled
  const HEDGE_FLAGS_ALLOW_CLOSEBY =1; // allow close by
  //--- flags borders
  const HEDGE_FLAGS_FIRST = MTEnHedgeFlags::HEDGE_FLAGS_ALLOW_CLOSEBY;
  const HEDGE_FLAGS_ALL   = 1; // HEDGE_FLAGS_ALLOW_CLOSEBY
  }

?>