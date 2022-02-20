<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class get history
 */
class MTHistoryProtocol
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
   * @param MTOrder $history
   * @return MTRetCode
   */
  public function HistoryGet($ticket, &$history)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_TICKET => $ticket);
    //---
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_HISTORY_GET, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send history get failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer history get is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseHistory(MTProtocolConsts::WEB_CMD_HISTORY_GET, $answer, $history_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse history get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get object from json
    $history = $history_answer->GetFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param string $command command
   * @param  string $answer answer from server
   * @param  MTHistoryAnswer $history_answer
   * @return MTRetCode
   */
  private function ParseHistory($command, &$answer, &$history_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $history_answer = new MTHistoryAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $history_answer->RetCode = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($history_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if (($history_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param  string $answer - answer from server
   * @param  MTHistoryPageAnswer $history_answer
   * @return MTRetCode
   */
  private function ParseHistoryPage(&$answer, &$history_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != MTProtocolConsts::WEB_CMD_HISTORY_GET_PAGE) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $history_answer = new MTHistoryPageAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $history_answer->RetCode = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($history_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- get json
    if (($history_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Get total history for login
   * @param string $login - user login
   * @param int $from - date from
   * @param int $to - date to
   * @param int $total - count
   * @return MTRetCode
   */
  public function HistoryGetTotal($login, $from, $to, &$total)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_FROM => $from, MTProtocolConsts::WEB_PARAM_TO => $to
    );
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_HISTORY_GET_TOTAL, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send history get total failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer history get total is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseHistoryTotal($answer, $history_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse history get total failed: [' . $error_code . '] ' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get total
    $total = $history_answer->Total;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Get historys
   * @param int $login - number of ticket
   * @param int $from - from date in unix time
   * @param int $to - to date in unix time
   * @param int $offset - begin records number
   * @param int $total - total records need
   * @param array(MTOrder) $histories
   * @return MTRetCode
   */
  public function HistoryGetPage($login, $from, $to, $offset, $total, &$histories)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_FROM => $from, MTProtocolConsts::WEB_PARAM_TO => $to, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
    //---
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_HISTORY_GET_PAGE, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send history get page failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer history get page is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseHistoryPage($answer, $history_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse history get page failed: [' . $error_code . '] ' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get object from json
    $histories = $history_answer->GetArrayFromJson();
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Check answer from MetaTrader 5 server
   * @param  $answer string server answer
   * @param  $history_answer MTHistoryTotalAnswer
   * @return false
   */
  private function ParseHistoryTotal(&$answer, &$history_answer)
    {
    $pos = 0;
    //--- get command answer
    $command = $this->m_connect->GetCommand($answer, $pos);
    if ($command != MTProtocolConsts::WEB_CMD_HISTORY_GET_TOTAL) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $history_answer = new MTHistoryTotalAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {

        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $history_answer->RetCode = $param['value'];
          break;
        case MTProtocolConsts::WEB_PARAM_TOTAL:
          $history_answer->Total = (int)$param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($history_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }
  }
/**
 * Answer on request history_get_total
 */
class MTHistoryTotalAnswer
  {
  public $RetCode = '-1';
  public $Total = 0;
  }
/**
 * get history page answer
 */
class MTHistoryPageAnswer
  {
  public $RetCode = '-1';
  public $ConfigJson = '';
  /**
   * From json get class MTHistory
   * @return array(MTHistory)
   */
  public function GetArrayFromJson()
    {
    $objects = MTJson::Decode($this->ConfigJson);
    if ($objects == null) return null;
    $result = array();
    //---
    foreach ($objects as $obj)
      {
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
 * get history page answer
 */
class MTHistoryAnswer
  {
  public $RetCode = '-1';
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

?>