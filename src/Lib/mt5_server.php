<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * class for control server
 */
class MTServer
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
   * Restart server
   *
   * @return MTRetCode
   */
  public function Restart()
    {
    //--- send request
    if (!$this->m_connect->Send( MTProtocolConsts::WEB_CMD_SERVER_RESTART, '' ))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write( MTLoggerType::ERROR, 'send server restart failed' );
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read()) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write( MTLoggerType::ERROR, 'answer server restart is empty' );
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    $restart_answer = null;
    //---
    if (($error_code = $this->Parse( $answer, $restart_answer )) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write( MTLoggerType::ERROR, 'parse server restart failed: [' . $error_code . ']' . MTRetCode::GetError( $error_code ) );
      return $error_code;
      }
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * check answer from MetaTrader 5 server
   *
   * @param string           $answer - answer from server
   * @param  MTRestartAnswer $restart_answer
   *
   * @return MTRetCode
   */
  private function Parse(&$answer, &$restart_answer)
    {
    $pos = 0;
    //--- get command answer
    $command_real = $this->m_connect->GetCommand( $answer, $pos );
    if ($command_real != MTProtocolConsts::WEB_CMD_SERVER_RESTART) return MTRetCode::MT_RET_ERR_DATA;
    //---
    $restart_answer = new MTRestartAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam( $answer, $pos, $pos_end )) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $restart_answer->RetCode = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode( $restart_answer->RetCode )) != MTRetCode::MT_RET_OK) return $ret_code;
    //---
    return MTRetCode::MT_RET_OK;
    }
  }

/**
 * get restart answer
 */
class MTRestartAnswer
  {
  public $RetCode = '-1';
  }