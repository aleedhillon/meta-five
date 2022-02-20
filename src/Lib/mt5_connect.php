<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Create connect to MetaTrader 5 server
 */
class MTConnect
  {
  //--- The serial number must be within the range 0000-FFFF:
  //--- 0-3FFF (0-16383) — client commands.
  const MAX_CLIENT_COMMAND = 16383;
  //--- socket connect
  private $m_connect = null;
  //--- ip to mt5 server
  private $m_ip_mt5 = null;
  //--- port o mt5 server
  private $m_port_mt5 = null;
  //--- timeout
  private $m_timeout_connection = 5;
  //--- crypto random string 
  private $m_crypt_rand = "";
  //--- crypto array
  private $crypt_iv = null;
  //--- 
  private $m_aes_out = null;
  //---
  private $m_aes_in = null;
  /**
   * class crypt aes 256
   * @var MT5CryptAes256
   */
  private $m_crypt_out = null;
  //---
  private $m_crypt_in = null;
  //--- number of client packet
  private $m_client_command = 0;

  /**
   * Create MetaTrader 5 Web Api class
   *
   * @param  string $ip_mt5              host or ip for MetaTrader 5 server
   * @param  int    $port_mt5            port to MetaTrader 5 server
   * @param  int    $timeout_connection  time out of try connection to MetaTrader 5 server
   * @param bool    $is_crypt            - need crypt connection
   *
   * @return MTConnect
   */
  public function __construct($ip_mt5, $port_mt5, $timeout_connection, $is_crypt)
    {
    $this->m_ip_mt5             = $ip_mt5;
    $this->m_port_mt5           = $port_mt5;
    $this->m_timeout_connection = $timeout_connection;
    //-- if need  crypt lets begin
    $this->is_crypt         = $is_crypt;
    $this->m_client_command = 0;
    }

  /**
   * Create connection to MT5
   * @return boolean
   */
  private function CreateConnection()
    {
    //--- create socket
    if(!($this->m_connect = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)))
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, "socket create failed, " . $this->GetSocketError());
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- try create connection to server
    if(!socket_connect($this->m_connect, $this->m_ip_mt5, $this->m_port_mt5))
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, "socket connect failed to " . $this->m_ip_mt5 . ":" . $this->m_port_mt5 . ", " . $this->GetSocketError());
      return MTRetCode::MT_RET_ERR_CONNECTION;
      }
    //--- set block connection
    if(!socket_set_block($this->m_connect))
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, "socket connect failed to " . $this->m_ip_mt5 . ":" . $this->m_port_mt5 . ", " . $this->GetSocketError());
      return MTRetCode::MT_RET_ERR_NETWORK;
      }
    //--- select socket and listen to change in it
    $r = array($this->m_connect);
    $w = array($this->m_connect);
    $f = array($this->m_connect);
    //---
    switch(socket_select($r,$w,$f, $this->m_timeout_connection))
    {
      case 2:
        if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, "Connection Refused to " . $this->m_ip_mt5 . ':' . $this->m_port_mt5);
        return MTRetCode::MT_RET_ERR_CONNECTION;
      case 1:
        if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "Connected to " . $this->m_ip_mt5 . ':' . $this->m_port_mt5);
        break;
      case 0:
        if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, "Timeout to " . $this->m_ip_mt5 . ':' . $this->m_port_mt5);
        return MTRetCode::MT_RET_ERR_TIMEOUT;
    }
    //--- OK
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Get las terror from socket
   * @return string
   */
  private function GetSocketError()
    {
    if($this->m_connect) $errorcode = socket_last_error($this->m_connect);
    else
    $errorcode = socket_last_error();
    //---
    $errormsg = socket_strerror($errorcode);
    return "code: " . $errorcode . ", " . $errormsg;
    }

  /**
   * Close connection
   * @return void
   */
  public function Disconnect()
    {
    if($this->m_connect)
      {
      socket_close($this->m_connect);
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "connection to " . $this->m_ip_mt5 . ':' . $this->m_port_mt5 . " closed");
      }
    }

  /**
   * Authentication on MetaTrader 5 server
   * @return boolean
   */
  public function Connect()
    {
    //--- connect to server
    return $this->CreateConnection();
    }

  /**
   * Send data to MetaTrader 5 server
   *
   * @param string  $command       - command, for example AUTH_START, AUTH_ANSWER and etc.
   * @param  string $data
   * @param bool    $first_request bool is ot first
   *
   * @return bool
   */
  public function Send($command, $data, $first_request = false)
    {
    if(!$this->m_connect)
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'connection closed');
      return false;
      }
    //--- number packet
    $this->m_client_command++;
    //--- packet max, than first
    if($this->m_client_command > self::MAX_CLIENT_COMMAND) $this->m_client_command = 1;
    //--- create query
    $q = $command;
    //--- create string for query
    if(!empty($data))
      {
      $body_request = '';
      $q .= "|";
      foreach($data as $param => $value)
        {
        if($param == MTProtocolConsts::WEB_PARAM_BODYTEXT)
          {
          $body_request = $value;
          }
        else
          {
          $q .= $param . '=' . MTUtils::Quotes($value) . '|';
          }
        }
      $q .= "\r\n";
      //--- add body request
      if(!empty($body_request)) $q .= $body_request;
      }
    else $q .= "|\r\n";
    //---
    $query_body = mb_convert_encoding($q, "utf-16le", "utf-8");
    //--- if need we crypt packet, crypt did not for auth_start and auth_start_answer
    if($command != MTProtocolConsts::WEB_CMD_AUTH_START && $command != MTProtocolConsts::WEB_CMD_AUTH_ANSWER && $this->is_crypt)
      {
      $query_body = $this->CryptPacket($query_body,strlen($query_body), $len_query);
      }
    else $len_query = strlen($query_body);

    //--- send request
    $query_len = 0;
    if($first_request)
      {
      $header    = sprintf(MTProtocolConsts::WEB_PREFIX_WEBAPI, $len_query, $this->m_client_command);
      $query     = $header . '0' . $query_body;
      $query_len = strlen($header) + 1 + $len_query;
      }
    else
      {
      $header    = sprintf(MTProtocolConsts::WEB_PACKET_FORMAT, $len_query, $this->m_client_command);
      $query     = $header . '0' . $query_body;
      $query_len = strlen($header) + 1 + $len_query;
      }
    //---
    if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, 'send data: ' . $query . "\r\nlength data: " . $query_len);
    //--- send data to MetaTrader 5 server
    $send_data = socket_write($this->m_connect, $query, $query_len);
    if(!$send_data)
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "send data failed, " . $data . "," . $this->GetSocketError());
      return false;
      }
    //---
    return true;
    }

  /**
   * Crypt the packet
   *
   * @param string   $packet_body
   * @param int      $len_packet
   * @param int      $len_crypt_packet
   *
   * @internal param int $len_pack
   * @return string|null
   */
  private function CryptPacket($packet_body,$len_packet, &$len_crypt_packet)
    {
    $result = '';
    if($this->m_crypt_out == null)
      {
      $key               = $this->crypt_iv[0] . $this->crypt_iv[1];

      $this->m_crypt_out = new MT5CryptAes256(MTUtils::GetFromHex($key), strlen($key) / 2);
      //---
      $this->m_aes_out = $this->crypt_iv[2];
      $this->m_aes_out = MTUtils::GetFromHex($this->m_aes_out);
      }

    //--- check aes
    if(empty($this->m_aes_out))
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "packet did not crypt, aes out is empty");
      return null;
      }
    //---
    for($i = 0, $key = 16; $i < $len_packet; $i++)
      {
      if($key >= 16)
        {
        //--- get new key for xor
        $this->m_aes_out = $this->m_crypt_out->encryptBlock($this->m_aes_out);
        //---  key index is 0
        $key = 0;
        }
      //--- xor all bytes
      $result .= chr(ord($packet_body[$i]) ^ ord($this->m_aes_out[$key]));
      $key++;
      }
    $len_crypt_packet = $i;
    //---
    if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "crypt: '" . $packet_body . "' to '" . $result . "', length: " . $len_crypt_packet);
    //--- return crypt string
    return $result;
    }

  /**
   * @param array $packet_body
   * @param int   $len_packet
   *
   * @return string|null
   */
  private function DeCryptPacket($packet_body, $len_packet)
    {
    if($packet_body == null) return null;
    //---
    if($this->m_crypt_in == null)
      {
      $key              = $this->crypt_iv[0] . $this->crypt_iv[1];
      $this->m_crypt_in = new MT5CryptAes256(MTUtils::GetFromHex($key), strlen($key) / 2);
      //--- create aes in array
      $this->m_aes_in = $this->crypt_iv[3];
      $this->m_aes_in = MTUtils::GetFromHex($this->m_aes_in);
      }
    //---
    if(empty($this->m_aes_in))
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "packet did not decrypt, aes in is empty");
      return false;
      }
    $out_result = '';
    for($i = 0, $key = 16; $i < $len_packet; $i++)
      {
      if($key >= 16)
        {
        //--- get new key for xor
        $this->m_aes_in = $this->m_crypt_in->encryptBlock($this->m_aes_in);
        //---
        $key = 0;
        }
      //--- xor all bytes
      $out_result .= chr(ord($packet_body[$i]) ^ ord($this->m_aes_in[$key]));
      $key++;
      }
    return $out_result;
    }

  /**
   * Get data from MetaTrader 5 server
   *
   * @param bool $auth_packet wait the auth packet
   * @param bool $is_binary
   *
   * @return null|string
   */
  public function Read($auth_packet = false, $is_binary = false)
    {
    if(!$this->m_connect)
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'connection closed');
      return null;
      }
    //---
    $result = '';
    //---
    while(true)
      {
      $data = $this->ReadPacket($header);
      //--- check header of packet
      if($header == null) break;
      //---
      if($data == null && $header->SizeBody > 0)
        {
        if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "read incorrect packet, length " . $header->SizeBody . ", but data is null");
        break;
        }
      //--- if need decrypt packet do it
      if($this->is_crypt && !$auth_packet) $data = $this->DeCryptPacket($data, $header->SizeBody);
      //--- check number of packet
      if($header->NumberPacket != $this->m_client_command)
        {
        //--- check packet length
        if($header->SizeBody != 0)
          {
          if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "number of packet incorrect need: " . $this->m_client_command . ", but get " . $header->NumberPacket);
          }
        else
          {
          //--- this is PING packet
          if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "PING packet");
          }
        //--- read next packet
        continue;
        }
      //--- get result
      $result .= $data;
      //--- read to end
      if($header->Flag == 0) break;
      }
    //--- decoding data
    if($is_binary)
      {
      $pos        = strpos($result, "\n");
      $first_line = substr($result, 0, $pos);
      $result     = mb_convert_encoding($first_line, "utf-8", "utf-16le") . "\r\n" . substr($result, $pos);
      }
    else  $result = mb_convert_encoding($result, "utf-8", "utf-16le");
    //---
    if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, "result: " . $result);
    //--- return result
    return $result;
    }

  /**
   * Read packet
   *
   * @param MTHeaderProtocol $header
   *
   * @return void
   */
  private function ReadPacket(&$header)
    {
    $header = null;
    //---
    $count_read = socket_recv($this->m_connect, $header_data, MTHeaderProtocol::HEADER_LENGTH, MSG_WAITALL);
    //$header_data = socket_read($this->m_connect, MTHeaderProtocol::HEADER_LENGTH, PHP_BINARY_READ);
    //---
    if($count_read != MTHeaderProtocol::HEADER_LENGTH)
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::ERROR, 'incorrect header read,  ' . $count_read . ' bytes');
      return null;
      }
    //---
    if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, 'header:' . $header_data . ' length: ' . $count_read);
    //--- get header from request
    if(!($header = MTHeaderProtocol::GetHeader($header_data)))
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, 'incorrect header data "' . $header_data . '"');
      return null;
      }
    //---
    if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, 'size body: ' . $header->SizeBody . ' number package: ' . $header->NumberPacket . ' flag: ' . $header->Flag);
    //---
    $need_len     = $header->SizeBody;
    $read_len     = 0;
    $data         = '';
    $count_packet = 0;
    while($read_len < $need_len)
      {
      $count_read = socket_recv($this->m_connect, $temp_data, $need_len - $read_len, MSG_WAITALL); //socket_read($this->m_connect, $need_len - $read_len, PHP_BINARY_READ);
      //--- check data
      if($temp_data === false)
        {
        $error_code = socket_last_error($this->m_connect);
        $error_msg  = socket_strerror($error_code);
        if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, 'socket error [' . $error_code . '] ' . $error_msg);
        return null;
        }
      //--- try get all data
      $data .= $temp_data;
      $read_len += $count_read;
      $count_packet++;
      }
    //--- check length
    if($read_len != $header->SizeBody)
      {
      if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, 'incorrect size of block: ' . $data . "\r\nReal size: " . $read_len . ', Size from header: ' . $header->SizeBody);
      return null;
      }
    //--- log
    if(MTLogger::getIsWriteLog()) MTLogger::write(MTLoggerType::DEBUG, 'read all data ' . $read_len . ' bytes, ' . $count_packet . ' packets');
    //---
    return $data;
    }

  /**
   * Get command answer
   *
   * @param string $answer
   * @param int    $pos
   *
   * @return null|string
   */
  public function GetCommand(&$answer, &$pos)
    {
    $pos = mb_strpos($answer, '|', 0, 'UTF-8');
    if($pos > 0) return mb_substr($answer, 0, $pos);
    //---
    return null;
    }

  /**
   * Get next param
   *
   * @param string $answer  - answer from server
   * @param int    $pos     - position that begin find
   * @param int    $pos_end - position of end parametrs
   *
   * @return array|null
   */
  public function GetNextParam(&$answer, &$pos, &$pos_end)
    {
    if($pos_end < 0) $pos_end = mb_strpos($answer, "\r\n", 0, 'UTF-8');
    $pos_code = mb_strpos($answer, '|', $pos + 1, 'UTF-8');
    //---
    if($pos_code > 0 && $pos_code < $pos_end)
      {
      $params_str = mb_substr($answer, $pos + 1, $pos_code - $pos - 1);
      $params     = explode('=', $params_str, 2);
      if(count($params) < 2) return null;
      //---
      $pos = $pos_code;
      //---
      return array('name'  => strtoupper($params[0]),
                   'value' => $params[1]);
      }
    //---
    return null;
    }

  /**
   * Get json from answer
   *
   * @param string $answer
   * @param int    $pos
   *
   * @return null|string
   */
  public function GetJson(&$answer, &$pos)
    {
    //--- find json by first {
    $pos_code = mb_strpos($answer, "\n", $pos, 'UTF-8');
    if($pos_code > 0)
      {
      $json_str = trim(mb_substr($answer, $pos_code));
      //---
      $pos = strlen($answer);
      //---
      return $json_str;
      }
    //---
    return null;
    }

  /**
   * read binary in answer
   *
   * @param $answer
   *
   * @return null|string
   */
  public function GetBinary(&$answer)
    {
    //--- find binary by first {
    $pos_code = strpos($answer, "\n");
    if($pos_code > 0)
      {
      return substr($answer, $pos_code);
      }
    //---
    return null;
    }

  /**
   * Get code from string
   *
   * @param string $ret_code_string
   *
   * @return int
   */
  public static function GetRetCode($ret_code_string)
    {
    if(empty($ret_code_string)) return "";
    $p = explode(" ", $ret_code_string, 2);
    //---
    return (int)$p[0];
    }

  /**
   * @param string $crypt    hash random string from MT server
   * @param string $password password to connection mt server
   *
   * @return void
   */
  public function SetCryptRand($crypt, $password)
    {
    $this->m_crypt_rand = $crypt;
    $out                = md5(md5(mb_convert_encoding($password, 'utf-16le', 'utf-8'), true) . MTProtocolConsts::WEB_API_WORD);
    //---
    for($i = 0; $i < 16; $i++)
      {
      $out                = md5(MTUtils::GetFromHex(substr($this->m_crypt_rand, $i * 32, 32)) . MTUtils::GetFromHex($out));
      $this->crypt_iv[$i] = $out;
      }
    }
  }

?>