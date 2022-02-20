<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
//--- web api version
define("WebAPIVersion", 2190);
//--- web api date
define("WebAPIDate", "18 Oct 2019");
//--- including all classes
include "mt5_auth.php";
include "mt5_utils.php";
include "mt5_connect.php";
include "mt5_protocol.php";
include "MTLogger.php";
include "mt5_time.php";
include "mt5_common.php";
include "mt5_group.php";
include "mt5_symbol.php";
include "mt5_user.php";
include "mt5_deal.php";
include "MTOrderProtocol.php";
include "mt5_position.php";
include "mt5_history.php";
include "mt5_tick.php";
include "mt5_mail.php";
include "mt5_news.php";
include "mt5_ping.php";
include "mt5_trade.php";
include "mt5_custom.php";
include "mt5_server.php";
include "mt5_cryptaes256.php";
include "mt5_json.php";
include "mt5_retcode.php";
/**
 * Main web api class
 */
class MTWebAPI
  {
  /**
   * connection to MetaTrader5 server
   * @var MTConnect
   */
  private $m_connect = null;
  //--- name agent
  private $m_agent = '';
  //--- is set crypt connection
  private $m_is_crypt = true;

  public function __construct($agent = 'WebAPI', $file_path = '/tmp/', $is_crypt = true)
    {
    $this->m_agent    = $agent;
    $this->m_is_crypt = $is_crypt;
    MTLogger::Init($agent, true, $file_path);
    }

  /**
   * @param string $ip       - ip address server
   * @param int    $port     - port server
   * @param int    $timeout  - timeout for request
   * @param string $login    - user login
   * @param string $password - user password
   *
   * @return MTRetCode
   */
  public function Connect($ip, $port, $timeout, $login, $password)
    {
    //--- create connection class
    $this->m_connect = new MTConnect($ip, $port, $timeout, $this->m_is_crypt);
    //--- create connection
    if(($error_code = $this->m_connect->Connect()) != MTRetCode::MT_RET_OK) return $error_code;
    //--- authorization to MetaTrader 5 server
    $auth = new MTAuthProtocol($this->m_connect, $this->m_agent);
    //---
    $crypt_rand = '';
    if(($error_code = $auth->Auth($login, $password, $this->m_is_crypt, $crypt_rand)) != MTRetCode::MT_RET_OK)
      {
      //--- disconnect
      $this->Disconnect();
      return $error_code;
      }
    //--- if need crypt
    if($this->m_is_crypt) $this->m_connect->SetCryptRand($crypt_rand, $password);
    //---
    return MTRetCode::MT_RET_OK;
    }

  /**
   * Check connection
   * @return bool
   */
  public function IsConnected()
    {
    return $this->m_connect != null;
    }

  /**
   * Disconnect from MetaTrader 5 server
   * @return void
   */
  public function Disconnect()
    {
    if($this->m_connect) $this->m_connect->Disconnect();
    }

  /**
   * Get current time from server
   *
   * @param MTConTime $time - time
   *
   * @return MTRetCode
   */
  public function TimeGet(&$time)
    {
    $mt_time = new MTTimeProtocol($this->m_connect);
    return $mt_time->TimeGet($time);
    }

  /**
   * Get current time from server
   * @return int - time in unix format
   */
  public function TimeServer()
    {
    $mt_time = new MTTimeProtocol($this->m_connect);
    return $mt_time->TimeServer();
    }

  /**
   * Get common information
   *
   * @param MTConCommon $common
   *
   * @return MTRetCode
   */
  public function CommonGet(&$common)
    {
    $mt_common = new MTCommonProtocol($this->m_connect);
    return $mt_common->CommonGet($common);
    }

  /**
   * Get count of groups
   *
   * @param int $total - count groups
   *
   * @return MTRetCode
   */
  public function GroupTotal(&$total)
    {
    $mt_group = new MTGroupProtocol($this->m_connect);
    return $mt_group->GroupTotal($total);
    }

  /**
   * Get next group
   *
   * @param int        $pos        - position
   * @param MTConGroup $group_next - next group
   *
   * @return MTRetCode
   */
  public function GroupNext($pos, &$group_next)
    {
    $mt_group = new MTGroupProtocol($this->m_connect);
    return $mt_group->GroupNext($pos, $group_next);
    }

  /**
   * Get group by name
   *
   * @param string     $name - name group
   * @param MTConGroup $group
   *
   * @return MTRetCode
   */
  public function GroupGet($name, &$group)
    {
    $mt_group = new MTGroupProtocol($this->m_connect);
    return $mt_group->GroupGet($name, $group);
    }

  /**
   * Add or update group
   *
   * @param MTConGroup $group
   * @param MTConGroup $new_group
   *
   * @return MTRetCode
   */
  public function GroupAdd($group, &$new_group)
    {
    $mt_group = new MTGroupProtocol($this->m_connect);
    return $mt_group->GroupAdd($group, $new_group);
    }

  /**
   * Delete group by name
   *
   * @param string     $name - name group
   *
   * @return MTRetCode
   */
  public function GroupDelete($name)
    {
    $mt_group = new MTGroupProtocol($this->m_connect);
    return $mt_group->GroupDelete($name);
    }

  /**
   * Get count symbols
   *
   * @param int $total - get total symbols
   *
   * @return MTRetCode
   */
  public function SymbolTotal(&$total)
    {
    $symbol = new MTSymbolProtocol($this->m_connect);
    return $symbol->SymbolTotal($total);
    }

  /**
   * Get next symbol
   *
   * @param int         $pos
   * @param MTConSymbol $symbol_next
   *
   * @return MTRetCode
   */
  public function SymbolNext($pos, &$symbol_next)
    {
    $mt_symbol = new MTSymbolProtocol($this->m_connect);
    return $mt_symbol->SymbolNext($pos, $symbol_next);
    }

  /**
   * Get symbol
   *
   * @param string      $name
   * @param MTConSymbol $symbol
   *
   * @return MTRetCode
   */
  public function SymbolGet($name, &$symbol)
    {
    $mt_symbol = new MTSymbolProtocol($this->m_connect);
    return $mt_symbol->SymbolGet($name, $symbol);
    }

  /**
   * Get config symbol
   *
   * @param string      $name  - symbol name
   * @param string      $group - group name
   * @param MTConSymbol $symbol
   *
   * @return MTRetCode
   */
  public function SymbolGetGroup($name, $group, &$symbol)
    {
    $mt_symbol = new MTSymbolProtocol($this->m_connect);
    return $mt_symbol->SymbolGetGroup($name, $group, $symbol);
    }

  /**
   * Symbol add and update
   *
   * @param MTConSymbol     $symbol     - symbol need add
   * @param MTConSymbol     $new_symbol - symbol added to server
   *
   * @return MTRetCode
   */
  public function SymbolAdd($symbol, &$new_symbol)
    {
    $mt_symbol = new MTSymbolProtocol($this->m_connect);
    return $mt_symbol->SymbolAdd($symbol, $new_symbol);
    }

  /**
   * Symbol delete
   *
   * @param string $name
   *
   * @return MTRetCode
   */
  public function SymbolDelete($name)
    {
    $mt_symbol = new MTSymbolProtocol($this->m_connect);
    return $mt_symbol->SymbolDelete($name);
    }

  /**
   * Add user to server
   *
   * @param MTUser $user     - user add to server
   * @param MTUser $new_user - user added to server
   *
   * @return MTRetCode
   */
  public function UserAdd($user, &$new_user)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->Add($user, $new_user);
    }

  /**
   * Update user to server
   *
   * @param MTUser $user - user add to server
   * @param MTUser $new_user
   *
   * @return MTRetCode
   */
  public function UserUpdate($user, &$new_user)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->Update($user, $new_user);
    }

  /**
   * User delete from server
   *
   * @param int $login
   *
   * @return MTRetCode
   */
  public function UserDelete($login)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->Delete($login);
    }

  /**
   * Get user
   *
   * @param int    $login
   * @param MTUser $user
   *
   * @return MTRetCode
   */
  public function UserGet($login, &$user)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->Get($login, $user);
    }

  /**
   * Check login and password
   *
   * @param int    $login
   * @param string $password
   * @param string $type
   *
   * @return MTRetCode
   */
  public function UserPasswordCheck($login, $password, $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->PasswordCheck($login, $password, $type);
    }

  /**
   * User change password
   *
   * @param int    $login
   * @param string $new_password - new password
   * @param string $type
   *
   * @return MTRetCode
   */
  public function UserPasswordChange($login, $new_password, $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->PasswordChange($login, $new_password, $type);
    }

  /**
   * User deposit change
   *
   * @param int            $login
   * @param float          $new_deposit - new deposit
   * @param string         $comment     - comment
   * @param MTEnDealAction $type
   *
   * @return MTRetCode
   */
  public function UserDepositChange($login, $new_deposit, $comment, $type)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->DepositChange($login, $new_deposit, $comment, $type);
    }

  /**
   * Get account information
   *
   * @param int       $login
   * @param MTAccount $account
   *
   * @return MTRetCode
   */
  public function UserAccountGet($login, &$account)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->AccountGet($login, $account);
    }

  /**
   * Get list users login
   *
   * @param string     $group
   * @param array(int) $logins
   *
   * @return MTRetCode
   */
  public function UserLogins($group, &$logins)
    {
    $mt_user = new MTUserProtocol($this->m_connect);
    return $mt_user->UserLogins($group, $logins);
    }

  /**
   * Get order
   *
   * @param int     $ticket
   * @param MTOrder $order
   *
   * @return MTRetCode
   */
  public function OrderGet($ticket, &$order)
    {
    $mt_order = new MTOrderProtocol($this->m_connect);
    return $mt_order->OrderGet($ticket, $order);
    }

  /**
   * Get all user orders
   *
   * @param int $login - user login
   * @param int $total - count of orders
   *
   * @return MTRetCode
   */
  public function OrderGetTotal($login, &$total)
    {
    $mt_order = new MTOrderProtocol($this->m_connect);
    return $mt_order->OrderGetTotal($login, $total);
    }

  /**
   * Get orders by page
   *
   * @param int            $login  - user login
   * @param int            $offset - record begin
   * @param int            $total  - count needs orders
   * @param array(MTOrder) $orders
   *
   * @return MTRetCode
   */
  public function OrderGetPage($login, $offset, $total, &$orders)
    {
    $mt_order = new MTOrderProtocol($this->m_connect);
    return $mt_order->OrderGetPage($login, $offset, $total, $orders);
    }

  /**
   * Get position
   *
   * @param int        $login
   * @param string     $symbol
   * @param MTPosition $position
   *
   * @return MTRetCode
   */
  public function PositionGet($login, $symbol, &$position)
    {
    $mt_position = new MTPositionProtocol($this->m_connect);
    return $mt_position->PositionGet($login, $symbol, $position);
    }

  /**
   * Get all user positions
   *
   * @param int $login - user login
   * @param int $total - count of positions
   *
   * @return MTRetCode
   */
  public function PositionGetTotal($login, &$total)
    {
    $mt_position = new MTPositionProtocol($this->m_connect);
    return $mt_position->PositionGetTotal($login, $total);
    }

  /**
   * Get positions by page
   *
   * @param int               $login  - user login
   * @param int               $offset - record begin
   * @param int               $total  - count needs orders
   * @param array(MTPosition) $positions
   *
   * @return MTRetCode
   */
  public function PositionGetPage($login, $offset, $total, &$positions)
    {
    $mt_position = new MTPositionProtocol($this->m_connect);
    return $mt_position->PositionGetPage($login, $offset, $total, $positions);
    }

  /**
   * Get deal
   *
   * @param int    $ticket
   * @param MTDeal $deal
   *
   * @return MTRetCode
   */
  public function DealGet($ticket, &$deal)
    {
    $mt_deal = new MTDealProtocol($this->m_connect);
    return $mt_deal->DealGet($ticket, $deal);
    }

  /**
   * Get count deals
   *
   * @param int $login - user login
   * @param int $from  - from date
   * @param int $to    - to date
   * @param int $total - count of deals
   *
   * @return MTRetCode
   */
  public function DealGetTotal($login, $from, $to, &$total)
    {
    $mt_deal = new MTDealProtocol($this->m_connect);
    return $mt_deal->DealGetTotal($login, $from, $to, $total);
    }

  /**
   * Get orders by page
   *
   * @param int           $login  - user login
   * @param int           $from   - from date
   * @param int           $to     - to date
   * @param int           $offset - record begin
   * @param int           $total  - count needs orders
   * @param array(MTDeal) $deals
   *
   * @return MTRetCode
   */
  public function DealGetPage($login, $from, $to, $offset, $total, &$deals)
    {
    $mt_deal = new MTDealProtocol($this->m_connect);
    return $mt_deal->DealGetPage($login, $from, $to, $offset, $total, $deals);
    }

  /**
   * Get history
   *
   * @param int     $ticket
   * @param MTOrder $history
   *
   * @return MTRetCode
   */
  public function HistoryGet($ticket, &$history)
    {
    $mt_deal = new MTHistoryProtocol($this->m_connect);
    return $mt_deal->HistoryGet($ticket, $history);
    }

  /**
   * Get count deals
   *
   * @param int $login - user login
   * @param int $from  - from date
   * @param int $to    - to date
   * @param int $total - count of history
   *
   * @return MTRetCode
   */
  public function HistoryGetTotal($login, $from, $to, &$total)
    {
    $mt_deal = new MTHistoryProtocol($this->m_connect);
    return $mt_deal->HistoryGetTotal($login, $from, $to, $total);
    }

  /**
   * Get orders by page
   *
   * @param int            $login  - user login
   * @param int            $from   - from date
   * @param int            $to     - to date
   * @param int            $offset - record begin
   * @param int            $total  - count needs orders
   * @param array(MTOrder) $orders
   *
   * @return MTRetCode
   */
  public function HistoryGetPage($login, $from, $to, $offset, $total, &$orders)
    {
    $mt_deal = new MTHistoryProtocol($this->m_connect);
    return $mt_deal->HistoryGetPage($login, $from, $to, $offset, $total, $orders);
    }

  /**
   * Get last tickets
   *
   * @param string        $symbol
   * @param array(MTTick) $ticks
   *
   * @return MTRetCode
   */
  public function TickLast($symbol, &$ticks)
    {
    $mt_tick = new MTTickProtocol($this->m_connect);
    return $mt_tick->TickLast($symbol, $ticks);
    }

  /**
   * Get last tickets by symbol and group
   *
   * @param string        $symbol
   * @param string        $group
   * @param array(MTTick) $ticks
   *
   * @return MTRetCode
   */
  public function TickLastGroup($symbol, $group, &$ticks)
    {
    $mt_tick = new MTTickProtocol($this->m_connect);
    return $mt_tick->TickLastGroup($symbol, $group, $ticks);
    }

  /**
   * Get last tickets
   *
   * @param string            $symbol
   * @param array(MTTickStat) $tick_stat
   *
   * @return MTRetCode
   */
  public function TickStat($symbol, &$tick_stat)
    {
    $mt_tick = new MTTickProtocol($this->m_connect);
    return $mt_tick->TickStat($symbol, $tick_stat);
    }

  /**
   * Send mail to user
   *
   * @param string $to      - user login or mask
   * @param string $subject - subject of mail
   * @param string $text    - mail text, may be in html format
   *
   * @return MTRetCode
   */
  public function MailSend($to, $subject, $text)
    {
    $mt_mail = new MTMailProtocol($this->m_connect);
    return $mt_mail->MailSend($to, $subject, $text);
    }

  /**
   * Send news to users
   *
   * @param string $subject - subject of news
   * @param string $category
   * @param int    $language
   * @param int    $priority
   * @param string $text    - news text, may be in html format
   *
   * @return MTRetCode
   */
  public function NewsSend($subject, $category, $language, $priority, $text)
    {
    $mt_news = new MTNewsProtocol($this->m_connect);
    return $mt_news->NewsSend($subject, $category, $language, $priority, $text);
    }

  /**
   * Trade balance
   *
   * @param int                 $login user login
   * @param MTEnDealAction      $type
   * @param double              $balance
   * @param string              $comment
   * @param int                 $ticket
   * @param bool                $margin_check
   *
   * @return MTRetCode
   */
  public function TradeBalance($login, $type, $balance, $comment, &$ticket=null,$margin_check=true)
    {
    $mt_trade = new MTTradeProtocol($this->m_connect);
    return $mt_trade->TradeBalance($login, $type, $balance, $comment, $ticket,$margin_check);
    }

  /**
   * Send ping to server
   * @return MTRetCode
   */
  public function Ping()
    {
    $mt_ping = new MTPingProtocol($this->m_connect);
    return $mt_ping->PingSend();
    }

  /**
   * Send custom command to MT server
   *
   * @param string $command
   * @param array  $params
   * @param string $body
   * @param array  $answer
   * @param string $answer_body
   *
   * @return MTRetCode
   */
  public function CustomSend($command, $params, $body, &$answer, &$answer_body)
    {
    $mt_custom = new MTCustomProtocol($this->m_connect);
    return $mt_custom->CustomSend($command, $params, $body, $answer, $answer_body);
    }

  /**
   * Restart server wich connect
   * @return MTRetCode
   */
  public function ServerRestart()
    {
    $mt_server = new MTServer($this->m_connect);
    return $mt_server->Restart();
    }

  /**
   * Create class user
   * @return MTUser
   */
  public function UserCreate()
    {
    return MTUser::CreateDefault();
    }

  /**
   * Create class group
   * @return MTConGroup
   */
  public function GroupCreate()
    {
    return MTConGroup::CreateDefault();
    }

  /**
   * Create class symbol
   * @return MTConSymbol
   */
  public function SymbolCreate()
    {
    return MTConSymbol::CreateDefault();
    }

  /**
   * Set flag write logs
   *
   * @param bool $is_write need write logs
   *
   * @return void
   */
  public function SetLoggerIsWrite($is_write)
    {
    MTLogger::setIsWriteLog($is_write);
    }

  /**
   * Set path to write logs
   *
   * @param string $file_path
   *
   * @return void
   */
  public function SetLoggerFilePath($file_path)
    {
    MTLogger::setFilePath($file_path);
    }

  /**
   * Set prefix for log files
   *
   * @param string $prefix
   *
   * @return void
   */
  public function SetLoggerFilePrefix($prefix)
    {
    MTLogger::setFilePrefix($prefix);
    }

  /**
   * Set or unset flag write MTLoggerType::DEBUG logs
   *
   * @param bool $is_write
   *
   * @return void
   */
  public function SetLoggerWriteDebug($is_write)
    {
    MTLogger::setWriteDebug($is_write);
    }
  }

?>
