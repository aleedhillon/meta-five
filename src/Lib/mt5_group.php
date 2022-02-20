<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class for send request group_total, group_next, group_get
 */
class MTGroupProtocol
  {
  /**
   * connection to MetaTrader5 server
   * @var MTConnect
   */
  private $m_connect;

  //---
  public function __construct($connect)
    {
     $this->m_connect = $connect;
    }

  /**
   * Get total group
   *
   * @param int $total
   *
   * @return MTRetCode
   */
  public function GroupTotal(&$total)
    {
     //--- send request
     if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_TOTAL, ""))
       {
        if(MTLogger::getIsWriteLog())
           MTLogger::write(MTLoggerType::ERROR, 'send group total failed');
        return MTRetCode::MT_RET_ERR_NETWORK;
       }
     //--- get answer
     if(($answer = $this->m_connect->Read()) == null)
       {
        if(MTLogger::getIsWriteLog())
           MTLogger::write(MTLoggerType::ERROR, 'answer group total is empty');
        return MTRetCode::MT_RET_ERR_NETWORK;
       }
     //--- parse answer
     if(($error_code = $this->ParseGroupTotal($answer, $group)) != MTRetCode::MT_RET_OK)
       {
        if(MTLogger::getIsWriteLog())
           MTLogger::write(MTLoggerType::ERROR, 'parse group total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
        return $error_code;
       }
     //---
     $total = $group->Total;
     //---
     return MTRetCode::MT_RET_OK;
    }

  /**
   * Check answer from MetaTrader 5 server
   *
   * @param  string             $answer server answer
   * @param  MTGroupTotalAnswer $group_answer
   *
   * @return MTRetCode
   */
  private function ParseGroupTotal(&$answer, &$group_answer)
    {
     $pos = 0;
     //--- get command answer
     $command = $this->m_connect->GetCommand($answer, $pos);
     if($command != MTProtocolConsts::WEB_CMD_GROUP_TOTAL)
        return MTRetCode::MT_RET_ERR_DATA;
     //---
     $group_answer = new MTGroupTotalAnswer();
     //--- get param
     $pos_end = -1;
     while(($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
       {
        switch($param['name'])
          {
           case MTProtocolConsts::WEB_PARAM_RETCODE:
              $group_answer->RetCode = $param['value'];
           break;
           case MTProtocolConsts::WEB_PARAM_TOTAL:
              $group_answer->Total = (int)$param['value'];
           break;
          }
       }
     //--- check ret code
     if(($ret_code = MTConnect::GetRetCode($group_answer->RetCode)) != MTRetCode::MT_RET_OK)
        return $ret_code;
     //---
     return MTRetCode::MT_RET_OK;
    }

  /**
   * Get group config
   *
   * @param int        $pos - from 0 to total
   * @param MTConGroup $group_next
   *
   * @return MTRetCode
   */
  public function GroupNext($pos, &$group_next)
    {
    $pos = (int)$pos;
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_INDEX => $pos);
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_NEXT, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send group next failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer group next is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_NEXT, $answer, $group_answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse group next failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //--- get object from json
    $group_next = $group_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * check answer from MetaTrader 5 server
   *
   * @param string         $command - command
   * @param  string        $answer  - answer from server
   * @param  MTGroupAnswer $group_answer
   *
   * @return MTRetCode
   */
  private function ParseGroup($command, &$answer, &$group_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $group_answer = new MTGroupAnswer();
    //--- get param
    $pos_end = -1;
    while(($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
    {
      switch($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $group_answer->RetCode = $param['value'];
          break;
      }
    }
    //--- check ret code
    if(($ret_code = MTConnect::GetRetCode($group_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if(($group_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Add symbol
   *
   * @param MTConGroup $group
   * @param MTConGroup $new_group
   *
   * @return MTRetCode
   */
  public function GroupAdd($group, &$new_group)
    {
    $data = array(MTProtocolConsts::WEB_PARAM_BODYTEXT => $this->GetParams($group));
    //--- send request
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_ADD, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send group add failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer group add is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_ADD, $answer, $group_answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse group add failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //--- get object from json
    $new_group = $group_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Get params for send group
   *
   * @param MTConGroup $group - group information
   *
   * @return string - json
   */
  private function GetParams($group)
    {
    if(!empty($group->Symbols))
    {
      foreach($group->Symbols as &$groupSymbol)
      {
        if($groupSymbol->TradeMode == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->TradeMode = 'default';
        if($groupSymbol->ExecMode == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->ExecMode = 'default';
        if($groupSymbol->FillFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->FillFlags = 'default';
        if($groupSymbol->ExpirFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->ExpirFlags = 'default';
        if($groupSymbol->OrderFlags == MTEnOrderFlags::ORDER_FLAGS_NONE) $groupSymbol->OrderFlags = 'default';
        //---
        if($groupSymbol->SpreadDiff == MTConGroupSymbol::DEFAULT_VALUE_INT) $groupSymbol->SpreadDiff = 'default';
        if($groupSymbol->SpreadDiffBalance == MTConGroupSymbol::DEFAULT_VALUE_INT) $groupSymbol->SpreadDiffBalance = 'default';
        if($groupSymbol->StopsLevel == MTConGroupSymbol::DEFAULT_VALUE_INT) $groupSymbol->StopsLevel = 'default';
        if($groupSymbol->FreezeLevel == MTConGroupSymbol::DEFAULT_VALUE_INT) $groupSymbol->FreezeLevel = 'default';
        //---
        if($groupSymbol->VolumeMin == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeMin = 'default';
        if($groupSymbol->VolumeMinExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeMinExt = 'default';
        if($groupSymbol->VolumeMax == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeMax = 'default';
        if($groupSymbol->VolumeMaxExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeMaxExt = 'default';
        if($groupSymbol->VolumeStep == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeStep = 'default';
        if($groupSymbol->VolumeStepExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeStepExt = 'default';
        if($groupSymbol->VolumeLimit == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeLimit = 'default';
        if($groupSymbol->VolumeLimitExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->VolumeLimitExt = 'default';
        //---
        if($groupSymbol->MarginFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->MarginFlags = 'default';
        if($groupSymbol->MarginInitial == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginInitial = 'default';
        if($groupSymbol->MarginMaintenance == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginMaintenance = 'default';
        //---
        $this->GetMarginRateInitialForJson($groupSymbol);
        $this->GetMarginRateMaintenanceForJson($groupSymbol);
        //--- DEPRECATED
        if($groupSymbol->MarginLong == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginLong = 'default';
        if($groupSymbol->MarginShort == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginShort = 'default';
        if($groupSymbol->MarginLimit == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginLimit = 'default';
        if($groupSymbol->MarginStop == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginStop = 'default';
        if($groupSymbol->MarginStopLimit == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginStopLimit = 'default';
        //---
        if($groupSymbol->MarginRateLiquidity == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginRateLiquidity = 'default';
        $groupSymbol->MarginLiquidity = $groupSymbol->MarginRateLiquidity;
        
        if($groupSymbol->MarginHedged == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginHedged = 'default';
        if($groupSymbol->MarginRateCurrency == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->MarginRateCurrency = 'default';
        $groupSymbol->MarginCurrency = $groupSymbol->MarginRateCurrency;
        //---
        if($groupSymbol->SwapMode == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->SwapMode = 'default';
        if($groupSymbol->SwapLong == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->SwapLong = 'default';
        if($groupSymbol->SwapShort == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE) $groupSymbol->SwapShort = 'default';
        if($groupSymbol->Swap3Day == MTConGroupSymbol::DEFAULT_VALUE_INT) $groupSymbol->Swap3Day = 'default';
        //---
        if($groupSymbol->RETimeout == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->RETimeout = 'default';
        if($groupSymbol->IEFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->IEFlags = 'default';
        if($groupSymbol->IECheckMode == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->IECheckMode = 'default';
        if($groupSymbol->IETimeout == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->IETimeout = 'default';
        if($groupSymbol->IESlipProfit == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->IESlipProfit = 'default';
        if($groupSymbol->IESlipLosing == MTConGroupSymbol::DEFAULT_VALUE_UINT) $groupSymbol->IESlipLosing = 'default';

        if($groupSymbol->IEVolumeMax == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->IEVolumeMax = 'default';
        if($groupSymbol->IEVolumeMaxExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64) $groupSymbol->IEVolumeMaxExt = 'default';
        //--- remap BookDepthLimit to PermissionsBookdepth
        $groupSymbol->PermissionsBookdepth = $groupSymbol->BookDepthLimit;
      }
    }
    //---
    return json_encode($group);
    }

  /**
   * array MarginRateInitial for json
   *
   * @param MTConGroupSymbol $groupSymbol
   */
  private function GetMarginRateInitialForJson(&$groupSymbol)
    {
    //--- set data
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
       $groupSymbol->MarginInitialBuy = "default";
    else
      $groupSymbol->MarginInitialBuy = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY];
    //---
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
       $groupSymbol->MarginInitialSell = "default";
    else
      $groupSymbol->MarginInitialSell = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL];
    //---
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginInitialBuyLimit = "default";
    else
      $groupSymbol->MarginInitialBuyLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
//---
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
       $groupSymbol->MarginInitialSellLimit = "default";
    else
      $groupSymbol->MarginInitialSellLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
//---
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginInitialBuyStop = "default";
    else
      $groupSymbol->MarginInitialBuyStop = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
//---
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginInitialSellStop = "default";
    else
      $groupSymbol->MarginInitialSellStop = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
//---
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginInitialBuyStopLimit = "default";
    else
      $groupSymbol->MarginInitialBuyStopLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
//---
    if(!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginInitialSellStopLimit = "default";
    else
      $groupSymbol->MarginInitialSellStopLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }

  /**
   * array MarginRateInitial for json
   *
   * @param MTConGroupSymbol $groupSymbol
   */
  private function GetMarginRateMaintenanceForJson(&$groupSymbol)
    {
    $result = MTConSymbol::GetDefaultMarginRate();
       //--- set data
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceBuy = "default";
    else
      $groupSymbol->MarginMaintenanceBuy = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY];
    //---
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceSell = "default";
    else
      $groupSymbol->MarginMaintenanceSell = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL];
    //---
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceBuyLimit = "default";
    else
      $groupSymbol->MarginMaintenanceBuyLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
//---
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceSellLimit = "default";
    else
      $groupSymbol->MarginMaintenanceSellLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
//---
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceBuyStop = "default";
    else
      $groupSymbol->MarginMaintenanceBuyStop = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
//---
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceSellStop = "default";
    else
      $groupSymbol->MarginMaintenanceSellStop = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
//---
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceBuyStopLimit = "default";
    else
      $groupSymbol->MarginMaintenanceBuyStopLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
//---
    if(!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
      $groupSymbol->MarginMaintenanceSellStopLimit = "default";
    else
      $groupSymbol->MarginMaintenanceSellStopLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }

  /**
   * Get information about group by name
   *
   * @param string     $name - group name
   * @param MTConGroup $group
   *
   * @return MTRetCode
   */
  public function GroupGet($name, &$group)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $name);
    //--- send request
    if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_GET, $data))
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send group get by name ' . $name . ' failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- get answer
    if(($answer = $this->m_connect->Read()) == null)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer group get is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
    }
    //--- parse answer
    if(($error_code = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_GET, $answer, $group_answer)) != MTRetCode::MT_RET_OK)
    {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse group get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
    }
    //--- get object from json
    $group = $group_answer->GetFromJson();
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
  public function GroupDelete($name)
    {
     $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $name);
     //--- send request
     if(!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_DELETE, $data))
       {
        if(MTLogger::getIsWriteLog())
           MTLogger::write(MTLoggerType::ERROR, 'send group delete failed');
        return MTRetCode::MT_RET_ERR_NETWORK;
       }
     //--- get answer
     if(($answer = $this->m_connect->Read()) == null)
       {
        if(MTLogger::getIsWriteLog())
           MTLogger::write(MTLoggerType::ERROR, 'answer group delete is empty');
        return MTRetCode::MT_RET_ERR_NETWORK;
       }
     //--- parse answer
     if(($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_GROUP_DELETE, $answer)) != MTRetCode::MT_RET_OK)
       {
        if(MTLogger::getIsWriteLog())
           MTLogger::write(MTLoggerType::ERROR, 'parse group delete failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
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
     if($command_real != $command)
        return MTRetCode::MT_RET_ERR_DATA;
     //---
     $user_answer = new MTGroupAnswer();
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
     if(($ret_code = MTConnect::GetRetCode($user_answer->RetCode)) != MTRetCode::MT_RET_OK)
        return $ret_code;
     //---
     return MTRetCode::MT_RET_OK;
    }
  }

/**
 * Answer on request group_total
 */
class MTGroupTotalAnswer
  {
   public $RetCode = '-1';
   public $Total = 0;
  }

/**
 * get group info
 */
class MTGroupAnswer
  {
   public $RetCode = '-1';
   public $ConfigJson = '';

   /**
    * From json get class MTConGroup
    * @return MTConGroup
    */
   public function GetFromJson()
     {
      $obj = MTJson::Decode($this->ConfigJson);
     
      if($obj == null)
         return null;
         
      $result = new MTConGroup();
      //---
      $result->Group                = (string)$obj->Group;
      $result->Server               = (int)$obj->Server;
      $result->PermissionsFlags     = (int)$obj->PermissionsFlags;
      $result->AuthMode             = (int)$obj->AuthMode;
      $result->AuthPasswordMin      = (int)$obj->AuthPasswordMin;
      $result->AuthOTPMode          = (int)$obj->AuthOTPMode;
      $result->Company              = (string)$obj->Company;
      $result->CompanyPage          = (string)$obj->CompanyPage;
      $result->CompanyEmail         = (string)$obj->CompanyEmail;
      $result->CompanySupportPage   = (string)$obj->CompanySupportPage;
      $result->CompanySupportEmail  = (string)$obj->CompanySupportEmail;
      $result->CompanyCatalog       = (string)$obj->CompanyCatalog;
      $result->Currency             = (string)$obj->Currency;
      $result->CurrencyDigits       = (int)$obj->CurrencyDigits;
      $result->ReportsMode          = (int)$obj->ReportsMode;
      $result->ReportsFlags         = (int)$obj->ReportsFlags;
      $result->ReportsSMTP          = (string)$obj->ReportsSMTP;
      $result->ReportsSMTPLogin     = (string)$obj->ReportsSMTPLogin;
      $result->ReportsSMTPPass      = (string)$obj->ReportsSMTPPass;
      $result->NewsMode             = (int)$obj->NewsMode;
      $result->NewsCategory         = (string)$obj->NewsCategory;
      $result->NewsLangs            = (array)$obj->NewsLangs;
      $result->MailMode             = (int)$obj->MailMode;
      $result->TradeFlags           = (int)$obj->TradeFlags;
      $result->TradeTransferMode    = (int)$obj->TradeTransferMode;
      $result->TradeInterestrate    = (float)$obj->TradeInterestrate;
      $result->TradeVirtualCredit   = (float)$obj->TradeVirtualCredit;
      $result->MarginMode           = (int)$obj->MarginMode;
      $result->MarginSOMode         = (int)$obj->MarginSOMode;
      $result->MarginFreeMode       = (int)$obj->MarginFreeMode;
      $result->MarginCall           = (float)$obj->MarginCall;
      $result->MarginStopOut        = (float)$obj->MarginStopOut;
      $result->MarginFreeProfitMode = (int)$obj->MarginFreeProfitMode;
      $result->DemoLeverage         = (int)$obj->DemoLeverage;
      $result->DemoDeposit          = (float)$obj->DemoDeposit;
      $result->LimitHistory         = (int)$obj->LimitHistory;
      $result->LimitOrders          = (int)$obj->LimitOrders;
      $result->LimitSymbols         = (int)$obj->LimitSymbols;
      $result->LimitPositions       = (int)$obj->LimitPositions;
      $result->Commissions          = $obj->Commissions;
      //---
      $result->Symbols = array();
      //---
      foreach($obj->Symbols as $symbol)
        {
         $s = new MTConGroupSymbol();
         //--- copy data from json object to MTConGroupSymbol
         $s->Path              = (string)$symbol->Path;
         $s->TradeMode         = $symbol->TradeMode == 'default' ? MTConGroupSymbol::GetDefault('trademode') : (int)$symbol->TradeMode;
         $s->ExecMode          = $symbol->ExecMode == 'default' ? MTConGroupSymbol::GetDefault('execmode') : (int)$symbol->ExecMode;
         $s->FillFlags         = $symbol->FillFlags == 'default' ? MTConGroupSymbol::GetDefault('fillflags') : (int)$symbol->FillFlags;
         $s->ExpirFlags        = $symbol->ExpirFlags == 'default' ? MTConGroupSymbol::GetDefault('expirflags') : (int)$symbol->ExpirFlags;
         $s->OrderFlags        = $symbol->OrderFlags == 'default' ? MTEnOrderFlags::ORDER_FLAGS_NONE : (int)$symbol->OrderFlags;
         $s->SpreadDiff        = $symbol->SpreadDiff == 'default' ? MTConGroupSymbol::GetDefault('spreaddiff') : (int)$symbol->SpreadDiff;
         $s->SpreadDiffBalance = $symbol->SpreadDiffBalance == 'default' ? MTConGroupSymbol::GetDefault('spreaddiffbalance') : (int)$symbol->SpreadDiffBalance;
         $s->StopsLevel        = $symbol->StopsLevel == 'default' ? MTConGroupSymbol::GetDefault('stopslevel') : (int)$symbol->StopsLevel;
         $s->FreezeLevel       = $symbol->FreezeLevel == 'default' ? MTConGroupSymbol::GetDefault('freezelevel') : (int)$symbol->FreezeLevel;
         //---
         $s->VolumeMin         = $symbol->VolumeMin == 'default' ? MTConGroupSymbol::GetDefault('volumemin') : (int)$symbol->VolumeMin;
         if(isset($symbol->VolumeMinExt))
           $s->VolumeMinExt    = $symbol->VolumeMinExt == 'default' ? MTConGroupSymbol::GetDefault('volumemin') : (int)$symbol->VolumeMinExt;
         else
            $s->VolumeMinExt   = $s->VolumeMin == MTConGroupSymbol::GetDefault('volumemin') ? $s->VolumeMin : MTUtils::ToNewVolume($s->VolumeMin);
         //---
         $s->VolumeMax         = $symbol->VolumeMax == 'default' ? MTConGroupSymbol::GetDefault('volumemax') : (int)$symbol->VolumeMax;
         if(isset($symbol->VolumeMaxExt))
           $s->VolumeMaxExt    = $symbol->VolumeMaxExt == 'default' ? MTConGroupSymbol::GetDefault('volumemax') : (int)$symbol->VolumeMaxExt;
         else
            $s->VolumeMaxExt   = $s->VolumeMax == MTConGroupSymbol::GetDefault('volumemax') ? $s->VolumeMax : MTUtils::ToNewVolume($s->VolumeMax);
         //---
         $s->VolumeStep        = $symbol->VolumeStep == 'default' ? MTConGroupSymbol::GetDefault('volumestep') : (int)$symbol->VolumeStep;
         if(isset($symbol->VolumeStepExt))
           $s->VolumeStepExt   = $symbol->VolumeStepExt == 'default' ? MTConGroupSymbol::GetDefault('volumestep') : (int)$symbol->VolumeStepExt;
         else
            $s->VolumeStepExt  = $s->VolumeStep == MTConGroupSymbol::GetDefault('volumestep') ? $s->VolumeStep : MTUtils::ToNewVolume($s->VolumeStep);
         //---
         $s->VolumeLimit       = $symbol->VolumeLimit == 'default' ? MTConGroupSymbol::GetDefault('volumelimit') : (int)$symbol->VolumeLimit;
         if(isset($symbol->VolumeLimitExt))
           $s->VolumeLimitExt  = $symbol->VolumeLimitExt == 'default' ? MTConGroupSymbol::GetDefault('volumelimit') : (int)$symbol->VolumeLimitExt;
         else
            $s->VolumeLimitExt = $s->VolumeLimit == MTConGroupSymbol::GetDefault('volumelimit') ? $s->VolumeLimit : MTUtils::ToNewVolume($s->VolumeLimit);
         //---
         $s->MarginFlags       = $symbol->MarginFlags == 'default' ? MTConGroupSymbol::GetDefault('marginflags') : (int)$symbol->MarginFlags;
         $s->MarginInitial     = $symbol->MarginInitial == 'default' ? MTConGroupSymbol::GetDefault('margininitial') : (float)$symbol->MarginInitial;
         $s->MarginMaintenance = $symbol->MarginMaintenance == 'default' ? MTConGroupSymbol::GetDefault('marginmaintenance') : (float)$symbol->MarginMaintenance;
         //---
         $this->SetMarginRateInitial($s, $symbol);
         $s->MarginRateMaintenance = $this->GetMarginRateMaintenance($symbol);
         //---
         $s->MarginRateLiquidity = $symbol->MarginLiquidity == 'default' ? MTConGroupSymbol::GetDefault('marginrateliquidity') : (float)$symbol->MarginLiquidity;
         $s->MarginHedged        = $symbol->MarginHedged == 'default' ? MTConGroupSymbol::GetDefault('marginhedged') : (float)$symbol->MarginHedged;
         $s->MarginRateCurrency  = $symbol->MarginCurrency == 'default' ? MTConGroupSymbol::GetDefault('marginratecurrency') : (float)$symbol->MarginCurrency;
         //---
         $s->SwapMode          = $symbol->SwapMode == 'default' ? MTConGroupSymbol::GetDefault('swapmode') : (int)$symbol->SwapMode;
         $s->SwapLong          = $symbol->SwapLong == 'default' ? MTConGroupSymbol::GetDefault('swaplong') : (float)$symbol->SwapLong;
         $s->SwapShort         = $symbol->SwapShort == 'default' ? MTConGroupSymbol::GetDefault('swapshort') : (float)$symbol->SwapShort;
         $s->Swap3Day          = $symbol->Swap3Day == 'default' ? MTConGroupSymbol::GetDefault('swap3day') : (int)$symbol->Swap3Day;
         $s->REFlags           = $symbol->REFlags == 'default' ? MTConGroupSymbol::GetDefault('reflags') : (int)$symbol->REFlags;
         $s->RETimeout         = $symbol->RETimeout == 'default' ? MTConGroupSymbol::GetDefault('retimeout') : (int)$symbol->RETimeout;
         $s->IEFlags           = $symbol->IEFlags == 'default' ? MTConGroupSymbol::GetDefault('ieflags') : (int)$symbol->IEFlags;
         $s->IECheckMode       = $symbol->IECheckMode == 'default' ? MTConGroupSymbol::GetDefault('iecheckmode') : (int)$symbol->IECheckMode;
         $s->IETimeout         = $symbol->IETimeout == 'default' ? MTConGroupSymbol::GetDefault('ietimeout') : (int)$symbol->IETimeout;
         $s->IESlipProfit      = $symbol->IESlipProfit == 'default' ? MTConGroupSymbol::GetDefault('ieslipprofit') : (int)$symbol->IESlipProfit;
         $s->IESlipLosing      = $symbol->IESlipLosing == 'default' ? MTConGroupSymbol::GetDefault('iesliplosing') : (int)$symbol->IESlipLosing;
         //---
         $s->IEVolumeMax       = $symbol->IEVolumeMax == 'default' ? MTConGroupSymbol::GetDefault('ievolumemax') : (int)$symbol->IEVolumeMax;
         if(isset($symbol->IEVolumeMaxExt))
           $s->IEVolumeMaxExt  = $symbol->IEVolumeMaxExt == 'default' ? MTConGroupSymbol::GetDefault('ievolumemax') : (int)$symbol->IEVolumeMaxExt;
         else
            $s->IEVolumeMaxExt = $s->IEVolumeMax == MTConGroupSymbol::GetDefault('ievolumemax') ? $s->IEVolumeMax : MTUtils::ToNewVolume($s->IEVolumeMax);
         //---
         if(isset($symbol->PermissionsFlags))
            $s->PermissionsFlags = (int)$symbol->PermissionsFlags;
         else
            $s->PermissionsFlags =MTEnGroupSymbolPermissions::PERMISSION_BOOK;
         if(isset($symbol->PermissionsBookdepth))
            $s->BookDepthLimit   = (int)$symbol->PermissionsBookdepth;
         else
            $s->BookDepthLimit   = 0;
         //---
         $result->Symbols[] = $s;
        }
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
     if(isset($obj->MarginInitialBuy)) $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $obj->MarginMaintenanceBuy;
     if(isset($obj->MarginInitialSell)) $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $obj->MarginMaintenanceSell;
     if(isset($obj->MarginInitialBuyLimit)) $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] = $obj->MarginMaintenanceBuyLimit;
     if(isset($obj->MarginInitialSellLimit)) $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $obj->MarginMaintenanceSellLimit;
     if(isset($obj->MarginInitialBuyStop)) $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] = $obj->MarginMaintenanceBuyStop;
     if(isset($obj->MarginInitialSellStop)) $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $obj->MarginMaintenanceSellStop;
     if(isset($obj->MarginInitialBuyStopLimit)) $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] = $obj->MarginMaintenanceBuyStopLimit;
     if(isset($obj->MarginInitialSellStopLimit)) $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $obj->MarginMaintenanceSellStopLimit;
     //---
     return $result;
    }
  }

/**
 * group permissions flags
 */
class MTEnPermissionsFlags
  {
  const PERMISSION_NONE = 0; // default
  const PERMISSION_CERT_CONFIRM = 1; // certificate confirmation neccessary
  const PERMISSION_ENABLE_CONNECTION = 2; // clients connections allowed
  const PERMISSION_RESET_PASSWORD = 4; // reset password after first logon
  const PERMISSION_FORCED_OTP_USAGE = 8;  // forced usage OTP
  const PERMISSION_RISK_WARNING = 16; // show risk warning window on start
  const PERMISSION_REGULATION_PROTECT = 32; // country-specific regulatory protection
  //--- enumeration borders
  const PERMISSION_ALL = 63;
  }

/**
 * authorization mode
 */
class MTEnAuthMode
  {
  const AUTH_STANDARD   = 0; // standard authorization
  const AUTH_RSA1024    = 1; // RSA1024 certificate
  const AUTH_RSA2048    = 2; // RSA2048 certificate
  const AUTH_RSA_CUSTOM = 3; // RSA custom
  //--- enumeration borders
  const AUTH_FIRST = MTEnAuthMode::AUTH_STANDARD;
  const AUTH_LAST  = MTEnAuthMode::AUTH_RSA_CUSTOM;
  }

/**
 * Oen-Time-Password mode
 */
class MTEnAuthOTPMode
  {
  const AUTH_OTP_DISABLED        = 0;
  const AUTH_OTP_TOTP_SHA256     = 1;
  const AUTH_OTP_TOTP_SHA256_WEB = 2;
  //--- enumeration borders
  const AUTH_OTP_FIRST = MTEnAuthOTPMode::AUTH_OTP_DISABLED;
  const AUTH_OTP_LAST  = MTEnAuthOTPMode::AUTH_OTP_TOTP_SHA256_WEB;
  }
  
/**
 * reports generation mode
 */
class MTEnReportsMode
  {
  const REPORTS_DISABLED = 0; // reports disabled
  const REPORTS_STANDARD = 1; // standard mode
  //--- enumeration borders
  const REPORTS_FIRST = MTEnReportsMode::REPORTS_DISABLED;
  const REPORTS_LAST  = MTEnReportsMode::REPORTS_STANDARD;
  }

/**
 * reports generation flags
 */
class MTEnReportsFlags
  {
  const REPORTSFLAGS_NONE    = 0; // none
  const REPORTSFLAGS_EMAIL   = 1; // send reports through email
  const REPORTSFLAGS_SUPPORT = 2; // send reports copies on support email
  //--- enumeration borders
  const REPORTSFLAGS_ALL = 3;
  }

/**
 * news modes
 */
class MTEnNewsMode
  {
  const NEWS_MODE_DISABLED = 0; // disable news
  const NEWS_MODE_HEADERS  = 1; // enable only news headers
  const NEWS_MODE_FULL     = 2; // enable full news
  //--- enumeration borders
  const NEWS_MODE_FIRST = MTEnNewsMode::NEWS_MODE_DISABLED;
  const NEWS_MODE_LAST  = MTEnNewsMode::NEWS_MODE_FULL;
  }

/**
 * internal email modes
 */
class MTEnMailMode
  {
  const MAIL_MODE_DISABLED = 0; // disable internal email
  const MAIL_MODE_FULL     = 1; // enable internal email
  //--- enumeration borders
  const MAIL_MODE_FIRST = MTEnMailMode::MAIL_MODE_DISABLED;
  const MAIL_MODE_LAST  = MTEnMailMode::MAIL_MODE_FULL;
  }

/**
 * client history limits
 */
class MTEnHistoryLimit
  {
  const TRADE_HISTORY_ALL      = 0; // unlimited
  const TRADE_HISTORY_MONTHS_1 = 1; // one month
  const TRADE_HISTORY_MONTHS_3 = 2; // 3 months
  const TRADE_HISTORY_MONTHS_6 = 3; // 6 months
  const TRADE_HISTORY_YEAR_1   = 4; // 1 year
  const TRADE_HISTORY_YEAR_2   = 5; // 2 years
  const TRADE_HISTORY_YEAR_3   = 6; // 3 years
  //--- enumeration borders
  const TRADE_HISTORY_FIRST = MTEnHistoryLimit::TRADE_HISTORY_ALL;
  const TRADE_HISTORY_LAST  = MTEnHistoryLimit::TRADE_HISTORY_YEAR_3;
  }

/**
 * free margin calculation modes
 */
class MTEnFreeMarginMode
  {
  const FREE_MARGIN_NOT_USE_PL = 0; // don't use floating profit and loss
  const FREE_MARGIN_USE_PL     = 1; // use floating profit and loss
  const FREE_MARGIN_PROFIT     = 2; // use floating profit only
  const FREE_MARGIN_LOSS       = 3; // use floating loss only
  //--- enumeration borders
  const FREE_MARGIN_FIRST = MTEnFreeMarginMode::FREE_MARGIN_NOT_USE_PL;
  const FREE_MARGIN_LAST  = MTEnFreeMarginMode::FREE_MARGIN_LOSS;
  }

/**
 * EnTransferMode
 */
class MTEnTransferMode
  {
  const TRANSFER_MODE_DISABLED   = 0;
  const TRANSFER_MODE_NAME       = 1;
  const TRANSFER_MODE_GROUP      = 2;
  const TRANSFER_MODE_NAME_GROUP = 3;
  //--- enumeration borders
  const TRANSFER_MODE_FIRST = MTEnTransferMode::TRANSFER_MODE_DISABLED;
  const TRANSFER_MODE_LAST  = MTEnTransferMode::TRANSFER_MODE_NAME_GROUP;
  }
  
/**
 * stop-out mode
 */
class MTEnStopOutMode
  {
  const STOPOUT_PERCENT = 0; // stop-out in percent
  const STOPOUT_MONEY   = 1; // stop-out in money
  //--- enumeration borders
  const STOPOUT_FIRST = MTEnStopOutMode::STOPOUT_PERCENT;
  const STOPOUT_LAST  = MTEnStopOutMode::STOPOUT_MONEY;
  }

/**
 * Mode of calculation of the free margin of the fixed income
 */
class MTEnMarginFreeProfitMode
  {
  const FREE_MARGIN_PROFIT_PL   = 0; // both fixed loss and profit on free margin
  const FREE_MARGIN_PROFIT_LOSS = 1; // only fixed loss on free margin
  //--- enumeration borders
  const FREE_MARGIN_PROFIT_FIRST = MTEnMarginFreeProfitMode::FREE_MARGIN_PROFIT_PL;
  const FREE_MARGIN_PROFIT_LAST  = MTEnMarginFreeProfitMode::FREE_MARGIN_PROFIT_LOSS;
  }

/**
 * group risk management mode
 */
class MTEnMarginMode
  {
  const MARGIN_MODE_RETAIL            = 0;  // Retail FX, Retail CFD, Retail Futures
  const MARGIN_MODE_EXCHANGE_DISCOUNT = 1;  // Exchange, margin discount rates based
  const MARGIN_MODE_RETAIL_HEDGED     = 2;  // Retail FX, Retail CFD, Retail Futures with hedged positions
  //--- enumeration borders
  const MARGIN_MODE_FIRST = MTEnMarginMode::MARGIN_MODE_RETAIL;
  const MARGIN_MODE_LAST  = MTEnMarginMode::MARGIN_MODE_RETAIL_HEDGED;
  }
  
/**
 * margin calculation flags
 */
class MTEnGroupMarginFlags
  {
  const MARGIN_FLAGS_NONE      = 0; // none
  const MARGIN_FLAGS_CLEAR_ACC = 1; // clear accumulated profit at end of day
  //--- enumeration borders
  const MARGIN_FLAGS_ALL = MTEnGroupMarginFlags::MARGIN_FLAGS_CLEAR_ACC;
  }

/**
 * trade rights flags
 */
class MTEnGroupTradeFlags
  {
  const TRADEFLAGS_NONE            = 0;   // none
  const TRADEFLAGS_SWAPS           = 1;   // allow swaps charges
  const TRADEFLAGS_TRAILING        = 2;   // allow trailing stops
  const TRADEFLAGS_EXPERTS         = 4;   // allow expert advisors
  const TRADEFLAGS_EXPIRATION      = 8;   // allow orders expiration
  const TRADEFLAGS_SIGNALS_ALL     = 16;  // allow trade signals
  const TRADEFLAGS_SIGNALS_OWN     = 32;  // allow trade signals only from own server
  const TRADEFLAGS_SO_COMPENSATION = 64;  // allow negative balance compensation after stop out
  //--- enumeration borders
  const TRADEFLAGS_DEFAULT = 31;
  const TRADEFLAGS_ALL     = 127;
  }

/**
 * Data config of group
 */
class MTConGroup
  {
  //--- group name
  public $Group;
  //--- group trade server ID
  public $Server;
  //--- MTEnPermissionsFlags
  public $PermissionsFlags;
  //--- MTEnAuthMode
  public $AuthMode;
  //--- minimal password length
  public $AuthPasswordMin;
  //--- OTP authentication mode (type is MTEnAuthOTPMode)
  public $AuthOTPMode;
  //--- company name
  public $Company;
  //--- company web page URL
  public $CompanyPage;
  //--- company email
  public $CompanyEmail;
  //--- company support site URL
  public $CompanySupportPage;
  //--- company support email
  public $CompanySupportEmail;
  //--- company catalog name (for reports and email templates)
  public $CompanyCatalog;
  //--- deposit currency
  public $Currency;
  public $CurrencyDigits;
  //--- MTEnReportsMode
  public $ReportsMode;
  //--- MTEnReportsFlags
  public $ReportsFlags;
  //--- reports SMTP server address:ports
  public $ReportsSMTP;
  //--- reports SMTP server login
  public $ReportsSMTPLogin;
  //--- reports SMTP server password
  public $ReportsSMTPPass;
  //--- MTEnNewsMode
  public $NewsMode;
  //--- news category filter string
  public $NewsCategory;
  //--- allowed news languages (Windows API LANGID used)
  public $NewsLangs;
  //--- MTEnMailMode
  public $MailMode;
  //--- MTEnGroupTradeFlags
  public $TradeFlags;
  //--- deposit transfer mode (type is MTEnTransferMode)
  public $TradeTransferMode;
  //--- interest rate for free deposit money
  public $TradeInterestrate;
  //--- virtual credit
  public $TradeVirtualCredit;
  //--- group risk management mode (type is MTEnMarginMode)
  public $MarginMode;
  //--- MTEnStopOutMode
  public $MarginSOMode;
  //--- MTEnFreeMarginMode
  public $MarginFreeMode;
  //--- Margin Call level value
  public $MarginCall;
  //--- Sto-Out level value
  public $MarginStopOut;
  //--- MTEnMarginFreeProfitMode
  public $MarginFreeProfitMode;
  //--- default demo accounts leverage
  public $DemoLeverage;
  //--- default demo accounts deposit
  public $DemoDeposit;
  //--- MTEnHistoryLimit
  public $LimitHistory;
  //--- max. order limit
  public $LimitOrders;
  //--- max. selected symbols limit
  public $LimitSymbols;
  //--- max. positions limit
  public $LimitPositions;
  //--- commissions
  public $Commissions;
  //--- groups symbols settings
  public $Symbols;

  /**
   * Create MTConGroup with default values
   * @return MTConGroup
   */
  public static function CreateDefault()
    {
    $group = new MTConGroup();
    //---
    $group->PermissionsFlags     = MTEnPermissionsFlags::PERMISSION_ENABLE_CONNECTION;
    $group->AuthMode             = MTEnAuthMode::AUTH_STANDARD;
    $group->AuthPasswordMin      = 7;
    $group->ReportsMode          = MTEnReportsMode::REPORTS_DISABLED;
    $group->ReportsFlags         = MTEnReportsFlags::REPORTSFLAGS_NONE;
    $group->Currency             = "USD";
    $group->CurrencyDigits       = 2;
    $group->NewsMode             = MTEnNewsMode::NEWS_MODE_FULL;
    $group->MailMode             = MTEnMailMode::MAIL_MODE_FULL;
    $group->MarginFreeMode       = MTEnFreeMarginMode::FREE_MARGIN_USE_PL;
    $group->MarginCall           = 50;
    $group->MarginStopOut        = 30;
    $group->MarginSOMode         = MTEnStopOutMode::STOPOUT_PERCENT;
    $group->TradeVirtualCredit   = 0;
    $group->MarginFreeProfitMode = MTEnMarginFreeProfitMode::FREE_MARGIN_PROFIT_PL;
    $group->DemoLeverage         = 0;
    $group->DemoDeposit          = 0;
    $group->LimitSymbols         = 0;
    $group->LimitOrders          = 0;
    $group->LimitHistory         = MTEnHistoryLimit::TRADE_HISTORY_ALL;
    $group->TradeInterestrate    = 0;
    $group->TradeFlags           = MTEnGroupTradeFlags::TRADEFLAGS_ALL;
    //---
    return $group;
    }
  }

/**
 * Requests Execution flags
 */
  class MTEnREFlags
  {
  const RE_FLAGS_NONE  = 0; // none
  const RE_FLAGS_ORDER = 1; // confirm orders after price confirmation
  //--- enumeration borders
  const RE_FLAGS_ALL = MTEnREFlags::RE_FLAGS_ORDER;
  }
      
/**
 * permissions
 */
class MTEnGroupSymbolPermissions
  {
  const PERMISSION_NONE = 0;
  const PERMISSION_BOOK = 1;
  //--- enumeration borders
  const PERMISSION_DEFAULT = MTEnGroupSymbolPermissions::PERMISSION_BOOK;
  const PERMISSION_ALL     = MTEnGroupSymbolPermissions::PERMISSION_BOOK;
  }

/**
 * Symbols configuration for clients group
 */
class MTConGroupSymbol
  {
  const DEFAULT_VALUE_UINT   = 0xffffffff;
  const DEFAULT_VALUE_INT    = 0x7fffffff;
  const DEFAULT_VALUE_UINT64 = 0xffffffffffffffff;
  const DEFAULT_VALUE_INT64  = 0x7fffffffffffffff;
  const DEFAULT_VALUE_DOUBLE = 1.7976931348623158e+308;
//--- symbol or symbol groups path
  public $Path;
  //--- MTEnTradeMode
  public $TradeMode;
  //--- MTEnCalcMode
  public $ExecMode;
  //--- MTEnFillingFlags
  public $FillFlags;
  //--- MTEnExpirationFlags
  public $ExpirFlags;
  //--- Flags trade orders (type is MTEnOrderFlags)
  public $OrderFlags;
  //--- spread difference (0 - floating spread)
  public $SpreadDiff;
  //--- spread difference balance
  public $SpreadDiffBalance;
  //--- stops level
  public $StopsLevel;
  //--- freeze level
  public $FreezeLevel;
  //--- minimal volume
  public $VolumeMin;
  //--- minimal volume
  public $VolumeMinExt;
  //--- maximal volume
  public $VolumeMax;
  //--- maximal volume
  public $VolumeMaxExt;
  //--- volume step
  public $VolumeStep;
  //--- volume step
  public $VolumeStepExt;
  //--- cumulative positions and orders limit
  public $VolumeLimit;
  //--- cumulative positions and orders limit
  public $VolumeLimitExt;
  //--- MTEnGroupMarginFlags
  public $MarginFlags;
  //--- initial margin
  public $MarginInitial;
  //--- maintenance margin
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
   * @deprecated
   * @var float 
   */
  public $MarginLong;
  /**
   * short orders and positions margin rate
   * @deprecated
   * @var float 
   */
  public $MarginShort;
  /**
   * limit orders and positions margin rate
   * @deprecated
   * @var float 
   */
  public $MarginLimit;
  /**
   * stop orders and positions margin rate
   * @deprecated
   * @var float 
   */
  public $MarginStop;
  /**
   * stop-limit orders and positions margin rate
   * @deprecated
   * @var float 
   */
  public $MarginStopLimit;
  //--- MTEnSwapMode
  public $SwapMode;
  //--- long positions swaps rate
  public $SwapLong;
  //--- short positions swaps rate
  public $SwapShort;
  //--- 3 time swaps day
  public $Swap3Day;
  //--- request execution flags (type is MTEnREFlags)
  public $REFlags;
  //--- instant execution
  public $RETimeout;
  //---
  public $IEFlags;
  //--- instant execution check mode
  public $IECheckMode;
  //--- instant execution timeout
  public $IETimeout;
  //--- instant execution profit slippage
  public $IESlipProfit;
  //--- instant execution losing slippage
  public $IESlipLosing;
  //--- instant execution max volume
  public $IEVolumeMax;
  //--- instant execution max volume
  public $IEVolumeMaxExt;
  //--- MTEnConSymbolPermissions
  public $PermissionsFlags;
  //--- book depth limit
  public $BookDepthLimit;

  /**
   * Create MTConGroupSymbol with default values
   * @return MTConGroupSymbol
   */
  public static function CreateDefault()
    {
    $groupSymbol = new MTConGroupSymbol();
    //---
    $groupSymbol->TradeMode         = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->ExecMode          = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->FillFlags         = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->ExpirFlags        = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->OrderFlags        = MTEnOrderFlags::ORDER_FLAGS_NONE;
    $groupSymbol->SpreadDiff        = MTConGroupSymbol::DEFAULT_VALUE_INT;
    $groupSymbol->SpreadDiffBalance = MTConGroupSymbol::DEFAULT_VALUE_INT;
    $groupSymbol->StopsLevel        = MTConGroupSymbol::DEFAULT_VALUE_INT;
    $groupSymbol->FreezeLevel       = MTConGroupSymbol::DEFAULT_VALUE_INT;
    $groupSymbol->VolumeMin         = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->VolumeMinExt      = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->VolumeMax         = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->VolumeMaxExt      = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->VolumeStep        = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->VolumeStepExt     = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->VolumeLimit       = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->VolumeLimitExt    = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->MarginFlags       = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->MarginInitial     = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->MarginMaintenance = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    //---
    $groupSymbol->MarginRateInitial = array(MTEnMarginRateTypes::MARGIN_RATE_BUY             => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                            MTEnMarginRateTypes::MARGIN_RATE_SELL            => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                            MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT       => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                            MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT      => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                            MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP        => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                            MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP       => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                            MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT  => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                            MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE);
    //---
    $groupSymbol->MarginRateMaintenance = array(MTEnMarginRateTypes::MARGIN_RATE_BUY             => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                                MTEnMarginRateTypes::MARGIN_RATE_SELL            => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                                MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT       => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                                MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT      => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                                MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP        => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                                MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP       => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                                MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT  => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
                                                MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE);
    //---
    $groupSymbol->MarginRateLiquidity = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->MarginHedged        = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->MarginRateCurrency  = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    //--- DEPRECATED
    $groupSymbol->MarginLong        = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->MarginShort       = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->MarginLimit       = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->MarginStop        = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->MarginStopLimit   = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    //---
    $groupSymbol->SwapMode          = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->SwapLong          = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->SwapShort         = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    $groupSymbol->Swap3Day          = MTConGroupSymbol::DEFAULT_VALUE_INT;
    $groupSymbol->REFlags           = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->RETimeout         = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->IEFlags           = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->IECheckMode       = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->IETimeout         = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->IESlipProfit      = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->IESlipLosing      = MTConGroupSymbol::DEFAULT_VALUE_UINT;
    $groupSymbol->IEVolumeMax       = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    $groupSymbol->IEVolumeMaxExt    = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
    //---
    $groupSymbol->PermissionsFlags =MTEnGroupSymbolPermissions::PERMISSION_DEFAULT;
    $groupSymbol->BookDepthLimit   =0;
    //---
    return $groupSymbol;
    }

  /**
   * Get default value by name
   *
   * @param string $name
   *
   * @return int|uint|float
   */
  public static function GetDefault($name)
    {
    switch(strtolower($name))
    {
      case "trademode":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "execmode":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "fillflags":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "expirflags":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "orderflags":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "spreaddiff":
        return MTConGroupSymbol::DEFAULT_VALUE_INT;
      case "spreaddiffbalance":
        return MTConGroupSymbol::DEFAULT_VALUE_INT;
      case "stopslevel":
        return MTConGroupSymbol::DEFAULT_VALUE_INT;
      case "freezelevel":
        return MTConGroupSymbol::DEFAULT_VALUE_INT;
      case "volumemin":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
      case "volumemax":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
      case "volumestep":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
      case "volumelimit":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
      case "marginflags":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "margininitial":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginmaintenance":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginrateliquidity":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginhedged":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginratecurrency":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "swapmode":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "swaplong":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "swapshort":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "swap3day":
        return MTConGroupSymbol::DEFAULT_VALUE_INT;
      case "retimeout":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "ieflags":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "iecheckmode":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "ietimeout":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "ieslipprofit":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "iesliplosing":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
      case "ievolumemax":
        return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
      //--- DEPRECATED
      case "marginlong":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginshort":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginlimit":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginstop":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
      case "marginstoplimit":
        return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
    }
    //---
    return MTConGroupSymbol::DEFAULT_VALUE_UINT;
    }
  }

/**
 * commission charge mode
 */
class MTEnCommissionMode
  {
  const COMM_MONEY_DEPOSIT = 0; // in money, in group deposit currency
  const COMM_MONEY_SYMBOL_BASE = 1; // in money, in symbol base currency
  const COMM_MONEY_SYMBOL_PROFIT = 2; // in money, in symbol profit currency
  const COMM_MONEY_SYMBOL_MARGIN = 3; // in money, in symbol margin currency
  const COMM_PIPS = 4; // in pips
  const COMM_PERCENT = 5; // in percent
  //--- enumeration borders
  const COMM_FIRST = MTEnCommissionMode::COMM_MONEY_DEPOSIT;
  const COMM_LAST  = MTEnCommissionMode::COMM_PERCENT;
  }
  
//--- commission type by volume
class MTEnCommissionVolumeType
  {
  const COMM_TYPE_DEAL = 0; // commission per deal
  const COMM_TYPE_VOLUME = 1; // commission per volume
  //--- enumeration borders
  const COMM_TYPE_FIRST = MTEnCommissionVolumeType::COMM_TYPE_DEAL;
  const COMM_TYPE_LAST  = MTEnCommissionVolumeType::COMM_TYPE_VOLUME;
  }

?>