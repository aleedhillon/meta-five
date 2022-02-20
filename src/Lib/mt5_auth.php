<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class authorization on MetaTrader 5 Server
 */
class MTAuthProtocol
  {
  private $m_connect = null;
  private $m_agent = '';
  /**
   * @param MTConnect $connect connection to server
   * @param string $agent - name of agent
   * @return \MTAuthProtocol
   *
   */
  public function __construct($connect, $agent)
    {
    $this->m_connect = $connect;
    $this->m_agent = $agent;
    }
  /**
   * Authorization on MetaTrader 5 server
   * @param string $login - manager login
   * @param string $password - manager password
   * @param bool $is_crypt - need crypt connection
   * @param string $crypt_rand - crypt rand string
   * @return MTRetCode
   */
  public function Auth($login, $password, $is_crypt, &$crypt_rand)
    {
    //--- send request to mt server
    if (($error_code = $this->SendAuthStart($login, $is_crypt, $auth_start_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'auth start failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- get code from hex string
    $rand_code = MTUtils::GetFromHex($auth_start_answer->SrvRand);
    //--- random string for MT server
    $random_cli_code = MTUtils::GetRandomHex(16);
    //--- get hash password with random code
    $hash = MTUtils::GetHashFromPassword($password, $rand_code);
    //--- send answer to server
    if (($error_code = $this->SendAuthAnswer($hash, $random_cli_code, $auth_answer)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'auth answer failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
      return $error_code;
      }
    //--- check password with another random code from MT server
    $hash_password = MTUtils::GetHashFromPassword($password, MTUtils::GetFromHex($random_cli_code));
    //--- check hash of password
    if ($hash_password != $auth_answer->CliRand)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'server sent incorrect password hash: is:' . $auth_answer->CliRand . ', my: ' . $hash_password);
      return MTRetCode::MT_RET_AUTH_SERVER_BAD;
      }
    //--- get crypt rand from MT server
    $crypt_rand = $auth_answer->CryptRand;
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Send AUTH_ANSWER to MT server
   * @param string $hash - password hash
   * @param string $random_cli_code client random string
   * @param MTAuthAnswer $auth_answer - result from server
   * @return MTRetCode
   */
  private function SendAuthAnswer($hash, $random_cli_code, &$auth_answer)
    {
    //--- send first request, with login, webapi version
    $data = array(MTProtocolConsts::WEB_PARAM_SRV_RAND_ANSWER => $hash, MTProtocolConsts::WEB_PARAM_CLI_RAND => $random_cli_code
    );
    //--- send request
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_AUTH_ANSWER, $data))
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'send auth answer failed');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- get answer
    if (($answer = $this->m_connect->Read(true)) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer auth answer is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseAuthAnswer($answer, $auth_answer, $error)) != MTRetCode::MT_RET_OK)
      {
      if (!empty($error)) if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse auth answer failed: ' . $error);
      return $error_code;
      }
    //--- ok
    return MTRetCode::MT_RET_OK;
    }
  /**
   * check answer from MetaTrader 5 server
   * @param  string $answer
   * @param  MTAuthStartAnswer $auth_answer
   * @param  string $error
   * @return MTRetCode
   */
  private function ParseAuthStart(&$answer, &$auth_answer, &$error)
    {
    $pos = 0;
    //--- get command answer
    $command = $this->m_connect->GetCommand($answer, $pos);
    if ($command != MTProtocolConsts::WEB_CMD_AUTH_START)
      {
      $error = 'type answer "' . $command . '" is incorrect, is not ' . MTProtocolConsts::WEB_CMD_AUTH_START;
      return MTRetCode::MT_RET_ERR_DATA;
      }
    //---
    $auth_answer = new MTAuthStartAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $auth_answer->RetCode = $param['value'];
          break;
        case MTProtocolConsts::WEB_PARAM_SRV_RAND:
          $auth_answer->SrvRand = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($error_code = MTConnect::GetRetCode($auth_answer->RetCode)) != MTRetCode::MT_RET_OK) return $error_code;
    //--- get srv rand
    if (empty($auth_answer->SrvRand) || $auth_answer->SrvRand == 'none')
      {
      $error = 'srv rand incorrect';
      return MTRetCode::MT_RET_ERR_PARAMS;
      }
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Send auth_start request
   * @param string $login  - user login
   * @param bool $is_crypt - need crypt protocol
   * @param MTAuthStartAnswer $auth_answer  - answer from server
   * @return MTRetCode
   */
  private function SendAuthStart($login, $is_crypt, &$auth_answer)
    {
    //--- send first request, with login, webapi version
    $data = array(MTProtocolConsts::WEB_PARAM_VERSION => WebAPIVersion, MTProtocolConsts::WEB_PARAM_AGENT => $this->m_agent, MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_TYPE => 'MANAGER', MTProtocolConsts::WEB_PARAM_CRYPT_METHOD => $is_crypt
            ? MTProtocolConsts::WEB_VAL_CRYPT_AES256OFB : MTProtocolConsts::WEB_VAL_CRYPT_NONE
    );

    //--- send request
    if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_AUTH_START, $data, true)) return MTRetCode::MT_RET_ERR_NETWORK;
    //--- get answer
    if (($answer = $this->m_connect->Read(true)) == null)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'answer auth start is empty');
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- parse answer
    if (($error_code = $this->ParseAuthStart($answer, $auth_answer, $error)) != MTRetCode::MT_RET_OK)
      {
      if (MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'parse auth start failed: [' . $error_code . ']' . $error);
      return $error_code;
      }
    //---
    return MTRetCode::MT_RET_OK;
    }
  /**
   * Parse answer from request AUTH_ANSWER
   * @param string $answer - answer from server
   * @param MTAuthAnswer $auth_answer - result
   * @param string $error
   * @return MTRetCode
   */
  private function ParseAuthAnswer($answer, &$auth_answer, &$error)
    {
    $pos = 0;
    //--- get command answer
    $command = $this->m_connect->GetCommand($answer, $pos);
    if ($command != MTProtocolConsts::WEB_CMD_AUTH_ANSWER)
      {
      $error = 'type answer "' . $command . '" is incorrect, is not ' . MTProtocolConsts::WEB_CMD_AUTH_ANSWER;
      return MTRetCode::MT_RET_ERR_DATA;
      }
    //---
    $auth_answer = new MTAuthAnswer();
    //--- get param
    $pos_end = -1;
    while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null)
      {
      switch ($param['name'])
      {
      //--- ret code
        case MTProtocolConsts::WEB_PARAM_RETCODE:
          $auth_answer->RetCode = $param['value'];
          break;
      //--- cli rand
        case MTProtocolConsts::WEB_PARAM_CLI_RAND_ANSWER:
          $auth_answer->CliRand = $param['value'];
          break;
      //--- crypt rand
        case MTProtocolConsts::WEB_PARAM_CRYPT_RAND:
          $auth_answer->CryptRand = $param['value'];
          break;
      }
      }
    //--- check ret code
    if (($ret_code = MTConnect::GetRetCode($auth_answer->RetCode)) != MTRetCode::MT_RET_OK) return $ret_code;
    //--- check CliRand
    if (empty($auth_answer->CliRand) || $auth_answer->CliRand == 'none')
      {
      $error = 'cli rand answer incorrect';
      return MTRetCode::MT_RET_ERR_PARAMS;
      }
    //---
    return MTRetCode::MT_RET_OK;
    }
  }

/**
 * Class get answer on request AUTH_START
 */
class MTAuthStartAnswer
  {
  public $RetCode = '-1';
  public $SrvRand = 'none';
  }

/**
 * Class get answer on request AUTH_ANSWER
 */
class MTAuthAnswer
  {
  public $RetCode = '-1';
  public $CliRand = 'none';
  public $CryptRand = '';
  }

?>
 
