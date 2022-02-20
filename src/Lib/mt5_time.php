<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
  /**
   * Class for send request time_server, time_get
   */
  class MTTimeProtocol
    {
    private $m_connect;
    public function __construct($connect)
      {
      $this->m_connect = $connect;
      }
    /**
     * Get current server time
     * @return int
     */
    public function TimeServer()
      {
      //--- send request
      if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TIME_SERVER, ""))
        {
       if(MTLogger::getIsWriteLog())  MTLogger::write(MTLoggerType::ERROR,'send time server failed');
        return 0;
        }
      //--- get answer
      if (($answer = $this->m_connect->Read()) == null)
        {
       if(MTLogger::getIsWriteLog())  MTLogger::write(MTLoggerType::ERROR,'answer time server start is empty');
        return 0;
        }
      //--- parse answer
      if (($error_code = $this->ParseTimeServer($answer, $time)) != MTRetCode::MT_RET_OK)
        {
       if(MTLogger::getIsWriteLog())  MTLogger::write(MTLoggerType::ERROR,'parse time server failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
        return 0;
        }
      //---
      return $time->GetUnixTime();
      }
    /**
     * check answer from MetaTrader 5 server
     * @param  $answer
     * @param  $time_answer MTTimeServerAnswer
     * @return MTRetCode
     */
    private function ParseTimeServer(&$answer, &$time_answer)
      {
      $pos = 0;
      //--- get command answer
      $command = $this->m_connect->GetCommand($answer, $pos);
      if ($command != MTProtocolConsts::WEB_CMD_TIME_SERVER) return MTRetCode::MT_RET_ERR_DATA;
      //---
      $time_answer = new MTTimeServerAnswer();
      //--- get param
      $pos_end = -1;
      while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
        {
        switch ($param['name'])
        {
          case MTProtocolConsts::WEB_PARAM_RETCODE:
            $time_answer->RetCode = $param['value'];
            break;
          case MTProtocolConsts::WEB_PARAM_TIME:
            $time_answer->Time = $param['value'];
            break;
        }
        }
      //--- check ret code
      if (($ret_code = MTConnect::GetRetCode($time_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
      //--- get time
      if (empty($time_answer->Time) || $time_answer->Time == 'none') return MTRetCode::MT_RET_ERR_PARAMS;
      //---
      return MTRetCode::MT_RET_OK;
      }
    /**
     * Get time config
     * @param $time MTConTime
     * @return MTRetCode
     */
    public function TimeGet(&$time)
      {
      //--- send request
      if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TIME_GET, ""))
        {
       if(MTLogger::getIsWriteLog())  MTLogger::write(MTLoggerType::ERROR,'send time get failed');
        return MTRetCode::MT_RET_ERR_NETWORK;
        }
      //--- get answer
      if (($answer = $this->m_connect->Read()) == null)
        {
       if(MTLogger::getIsWriteLog())  MTLogger::write(MTLoggerType::ERROR,'answer time get is empty');
        return MTRetCode::MT_RET_ERR_NETWORK;
        }
      //--- parse answer
      if (($error_code = $this->ParseTimeGet($answer, $time_answer)) != MTRetCode::MT_RET_OK)
        {
       if(MTLogger::getIsWriteLog())  MTLogger::write(MTLoggerType::ERROR,'parse time get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
        return $error_code;
        }
      //---
      $time = $time_answer->GetFromJson();
      //---
      return MTRetCode::MT_RET_OK;
      }
    /**
     * check answer from MetaTrader 5 server
     * @param  $answer
     * @param  $time_answer MTTimeGetAnswer
     * @return MTRetCode
     */
    private function ParseTimeGet(&$answer, &$time_answer)
      {
      $pos = 0;
      //--- get command answer
      $command = $this->m_connect->GetCommand($answer, $pos);
      if ($command != MTProtocolConsts::WEB_CMD_TIME_GET) return MTRetCode::MT_RET_ERR_DATA;
      //---
      $time_answer = new MTTimeGetAnswer();

      //--- get param
      $pos_end = -1;
      while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
        {
        switch ($param['name'])
        {
          case MTProtocolConsts::WEB_PARAM_RETCODE:
            $time_answer->RetCode = $param['value'];
            break;
        }
        }
      //--- check ret code
      if (($ret_code = MTConnect::GetRetCode($time_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
      //--- get json
      if (($time_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null) return MTRetCode::MT_RET_REPORT_NODATA;
      //---
      return MTRetCode::MT_RET_OK;
      }
    }

  /**
   * answer on request time_server
   */
  class MTTimeServerAnswer
    {
    public $RetCode = '-1';
    public $Time = 'none';
    /**
     * Get time in unix format
     * @return int
     */
    public function GetUnixTime()
      {
      $p = explode(" ", $this->Time, 2);
      return (int)$p[0];
      }
    }

  /**
   * answer on request time_get
   */
  class MTTimeGetAnswer
    {
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTConTime
     * @return MTConTime
     */
    public function GetFromJson()
      {
      $obj = MTJson::Decode($this->ConfigJson);
      if ($obj == null) return null;
      //---
      $result = new MTConTime();
      //---
      $result->Daylight      = (int)$obj->Daylight;
      $result->DaylightState = (int)$obj->DaylightState;
      $result->TimeServer    = (string)$obj->TimeServer;
      $result->TimeZone      = (int)$obj->TimeZone;
      $result->Days          = $obj->Days;
      //---
      $obj = null;
      //---
      return $result;
      }
    }

/**
 * day working mode
 */
class MTEnTimeTableMode
  {
  const TIME_MODE_DISABLED = 0; // work enabled
  const TIME_MODE_ENABLED  = 1; // work disabled
  //---
  const TIME_MODE_FIRST = MTEnTimeTableMode::TIME_MODE_DISABLED;
  const TIME_MODE_LAST  = MTEnTimeTableMode::TIME_MODE_ENABLED;
  }
    
/**
 * Time configuration
 */
class MTConTime
  {
  //--- daylight correction mode
  public $Daylight;
  public $DaylightState;
  //--- server timezone in minutes (0-GMT;-3600=GMT-1;3600=GMT+1)
  public $TimeZone;
  //--- time synchronization server address (TIME or NTP protocol)
  public $TimeServer;
  //--- days
  public $Days;
  }

?>