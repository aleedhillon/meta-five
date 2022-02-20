<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTCustomProtocol
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
   * Send custom command to MT server
   * @param string $command
   * @param array $params
   * @param string $body
   * @param array $answer_custom
   * @param string $answer_body
   * @return MTRetCode
   */
  public function CustomSend($command, $params, $body, &$answer_custom, &$answer_body)
    {
    //--- send request
    $data = $params;
    //---
    if (!empty($body))
      {
      if (empty($data)) $data = array();
      $data[MTProtocolConsts::WEB_PARAM_BODYTEXT] = $body;
      }
    //---
    if (!$this->m_connect->Send($command, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send custom command failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read(false,true)) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer custom command is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    $trade_answer = null;

    if (($error_code = $this->Parse($command, $answer, $answer_custom, $answer_body)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse custom command failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param string $command - command from server
   * @param string $answer - answer from server
   * @param  array $custom_answer
   * @param  string $answer_body
   * @return MTRetCode
   */
  private function Parse($command, &$answer, &$custom_answer, &$answer_body)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != $command) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $custom_answer = array();
    //--- get param
    $pos_end = -1;
    $ret_code = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $ret_code = $param['value'];
          break;
      }
      $custom_answer[$param['name']] = $param['value'];
      }
    //--- get body
    $answer_body = $this->m_connect->GetBinary($answer);
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($ret_code)) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }
  }

?>