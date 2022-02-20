<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with ping
 */
class MTPingProtocol
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
   * Ping to server
   * @return MTRetCode
   */
  public function PingSend()
    {
    //--- send request
    if (!$this->m_connect->Send('', null))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send ping failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //---
    return MTRetCode::MT_RET_OK;
    }
  }
?>