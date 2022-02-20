<?php
namespace AleeDhillon\MetaFive\Lib;
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class logger
 * Write log in file or standart out
 */
class MTLogger
  {
  //--- agent name
  private static $m_agent = '';
  //--- is write log
  private static $m_is_write_log = true;
  //--- file path when write file
  private static $m_file_path = '';
  //--- log file prefix
  private static $m_file_prefix = '';
  //--- what logs write, default write all logs
  private static $m_status_write;
  /**
   * @param string $agent - agent name
   * @param bool $is_write_log - is write any logs
   * @param string $file_path - file path, if not null all logs write in files
   * @param string $file_prefix - log file prefix
   */
  public static function Init($agent, $is_write_log = true, $file_path = '/tmp/', $file_prefix = '')
    {
    //--- set first data
    MTLogger::$m_agent = $agent;
    MTLogger::$m_is_write_log = $is_write_log;
    MTLogger::$m_file_prefix = $file_prefix;
    //---
    if (!empty($file_path))
      {
      MTLogger::setFilePath($file_path);
      }
    //--- write only error logs
    MTLogger::$m_status_write = MTLoggerType::ERROR;
    }
  /**
   * Function write message to file or standart output
   * @param int $type - type of error
   * @param string $message
   * @return void
   */
  public static function write($type, $message)
    {
    if (!MTLogger::$m_is_write_log) return;
    if (!(MTLogger::$m_status_write & $type)) return;
    //---
    $str = date("Y.m.d H:i:s", time()) . "\t" . $type . "\t" . MTLogger::$m_agent . ':' . $message."\r\n";
    //-- need write to file or standart output
    if (!empty(MTLogger::$m_file_path))
      {
      $filename = MTLogger::$m_file_path . MTLogger::$m_file_prefix . date('Y_m_d') . '.log';
      error_log((string)$str, 3, $filename);
      }
    else echo $str, "\r\n";
    }
  /**
   * Set new agent name
   * @param string $new_agent
   * @return void
   */
  public static function setAgent($new_agent)
    {
    MTLogger::$m_agent = $new_agent;
    }
  /**
   * Get current agent
   * @return string
   */
  public static function getAgent()
    {
    return MTLogger::$m_agent;
    }
  /**
   * Set flag is write log
   * @param bool $is_write_log
   * @return void
   */
  public static function setIsWriteLog($is_write_log)
    {
    MTLogger::$m_is_write_log = $is_write_log;
    }
  /**
   * Get flag is write log
   * @return bool
   */
  public static function getIsWriteLog()
    {
    return MTLogger::$m_is_write_log;
    }
  /**
   * Set file path where write logs
   * @param string $file_path
   * @return void
   */
  public static function setFilePath($file_path)
    {
    MTLogger::$m_file_path = $file_path;
    $last_symbol = MTLogger::$m_file_path[strlen(MTLogger::$m_file_path) - 1];
    if ($last_symbol != '/' && $last_symbol != '\\') MTLogger::$m_file_path .= '/';
    //--- create logs path
    if (!file_exists(MTLogger::$m_file_path)) mkdir(MTLogger::$m_file_path, 0777, true);
    }
  /**
   * Get current file path
   * @return string
   */
  public static function getFilePath()
    {
    return MTLogger::$m_file_path;
    }
  /**
   * Log files prefix
   * @param string $file_prefix
   * @return void
   */
  public static function setFilePrefix($file_prefix)
    {
    MTLogger::$m_file_prefix = $file_prefix;
    }
  /**
   * Get current log files prefix
   * @return string
   */
  public static function getFilePrefix()
    {
    return MTLogger::$m_file_prefix;
    }
  /**
   * Set or unset flag write MTLoggerType::DEBUG logs
   * @param bool $is_write
   * @return void
   */
  public static function setWriteDebug($is_write)
    {
    if ($is_write) MTLogger::$m_status_write |= MTLoggerType::DEBUG;
    else           MTLogger::$m_status_write &= ~MTLoggerType::DEBUG;
    }
  }
/**
 * Type of log
 */
class MTLoggerType
  {
  const DEBUG = 1;
  const ERROR = 2;
  }

?>
