<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTUtils
  {
  /**
   * Parsing hex string to string
   *
   * @param  string $str_hex
   *
   * @return string
   */
  public static function GetFromHex($str_hex)
    {
    $length = strlen($str_hex);
    if(($length % 2)) return 0;
    //---
    $result = '';
    for($i = 0; $i < $length; $i += 2)
      {
      $result .= chr(hexdec(substr($str_hex, $i, 2)));
      }
    //---
    return $result;
    }

  /**
   * From bytes to hex
   *
   * @param  array(byte) $bytes
   *
   * @return string
   */
  public static function GetHexFromBytes($bytes)
    {
    return bin2hex($bytes);
    }

  /**
   * From string to hex
   *
   * @param  string $str
   *
   * @return string
   */
  public static function GetHexFromString($str)
    {
    //---
    $result = '';
    for($i = 0; $i < strlen($str); $i++)
      {
      $result .= sprintf("%02x", ord($str[$i]));
      }
    //---
    return $result;
    }

  /**
   * Get random string hex format
   *
   * @param int $len - length of string
   *
   * @return string
   */
  public static function GetRandomHex($len)
    {
    $result = '';
    //---
    for($i = 0; $i < $len; $i++) $result .= sprintf("%02x", rand(0, 254));
    //---
    return $result;
    }

  /**
   * Get hash from password
   *
   * @param  string $password  - user password
   * @param string  $rand_code - random string
   *
   * @return string
   */
  public static function GetHashFromPassword($password, $rand_code)
    {
    //--- hash of password
    $password_hash = md5(mb_convert_encoding($password, 'utf-16le', 'utf-8'), true) . MTProtocolConsts::WEB_API_WORD;
    //--- hash for answer
    $hash = md5(md5($password_hash, true) . $rand_code);
    //---
    return $hash;
    }

  /**
   * add \ for special symbols: =, |, \n, \
   *
   * @param $str string
   *
   * @return string
   */
  public static function Quotes($str)
    {
    return str_replace(array('\\', '=', '|', "\n"), array('\\\\', '\=', '\|', "\\\n"), $str);
    }

  /**
   * Convert new 8-digits volume to old 4-digits format
   *
   * @param $new_volume int
   *
   * @return int
   */
  public static function ToOldVolume($new_volume)
    {
     return (int)$new_volume / 10000;
    }

  /**
   * Convert old 4-digits volume to new 8-digits format
   *
   * @param $old_volume int
   *
   * @return int
   */
  public static function ToNewVolume($old_volume)
    {
     return (int)$old_volume * 10000;
    }
  }

?>