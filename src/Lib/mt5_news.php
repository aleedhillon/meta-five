<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with news
 */
class MTNewsProtocol
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
   * Send news to users
   * @param string $subject - subject of news
   * @param string $category
   * @param int $language
   * @param int $priority
   * @param string $text - news text, may be in html format
   * @return MTRetCode
   */
  public function NewsSend($subject,$category,$language,$priority, $text)
    {
    //--- send request
    $data = array(MTProtocolConsts::WEB_PARAM_SUBJECT => $subject,MTProtocolConsts::WEB_PARAM_CATEGORY => $category,
                  MTProtocolConsts::WEB_PARAM_LANGUAGE=>$language,MTProtocolConsts::WEB_PARAM_PRIORITY=>$priority,
                  MTProtocolConsts::WEB_PARAM_BODYTEXT => $text);
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_NEWS_SEND, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send news failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer news is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    $tick_answer = null;
    //---
    if (($error_code = $this->Parse($answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse news answer failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
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
    if ($command_real != MTProtocolConsts::WEB_CMD_NEWS_SEND) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $news_answer = new MTNewsAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $news_answer->RetCode = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($news_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }
  }
/**
 * news answer
 */
class MTNewsAnswer
  {
  public $RetCode = '-1';
  }

?>