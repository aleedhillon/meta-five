<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with mail
 */
class MTMailProtocol
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
   * Send mail to user
   * @param string $to - user login or mask
   * @param string $subject - subject of mail
   * @param string $text - mail text, may be in html format
   * @return MTRetCode
   */
  public function MailSend($to, $subject, $text)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_TO => $to, MTProtocolConsts::WEB_PARAM_SUBJECT => $subject, MTProtocolConsts::WEB_PARAM_BODYTEXT => $text);
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_MAIL_SEND, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send mail failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer mail is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    $tick_answer = null;
    //---
    if (($error_code = $this->Parse($answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse mail answer failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param string $answer - answer from server
   * @return MTRetCode
   */
  private function Parse(&$answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand($answer, $pos);
    if ($command_real != MTProtocolConsts::WEB_CMD_MAIL_SEND) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $mail_answer = new MTMailAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $mail_answer->RetCode = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($mail_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }
  }
/**
 * get mail answer
 */
class MTMailAnswer
  {
  public $RetCode = '-1';
  }

?>