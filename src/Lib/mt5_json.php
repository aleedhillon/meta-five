<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTJson
  {
  /**
   * @static Decode json
   *
   * @param $json
   *
   * @return object
   */
  public static function Decode($json)
    {
    $res = json_decode($json, false);
    //--- if incorrect json, try replace charset
    if($res == null)
      {
      $res = json_decode(str_replace("\\", "\\\\", $json), false);
      }
    return $res;
    }

  /**
   * @static Encode object to json
   *
   * @param $obj
   *
   * @return string
   */
  public static function Encode($obj)
    {
    $json = json_encode($obj);
    //--- need replace \u symbol to utf8 symbol
    return preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', array('self', 'ParseUnicode'), $json);
    }

  /**
   * Convert \u - php unicode symbol to utf8 symbol
   * @static
   *
   * @param $matches
   *
   * @return string
   */
  private static function ParseUnicode($matches)
    {
    return html_entity_decode('&#' . hexdec($matches[1]) . ';', ENT_COMPAT, 'UTF-8');
    }
  }
