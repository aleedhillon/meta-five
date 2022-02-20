<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class work with header of protocol
 */
class MTHeaderProtocol
{
  //--- length of header
  const HEADER_LENGTH = 9;
  public $SizeBody;
  public $NumberPacket;
  public $Flag;

  /**
   * Get header of response from MetaTrader 5 server
   *
   * @param string $header_data -  package from server
   *
   * @return MTHeaderProtocol|null
   */
  public static function GetHeader($header_data)
    {
    if (strlen( $header_data ) < self::HEADER_LENGTH) return null;
    $result = new MTHeaderProtocol();
    //--- get size of answer
    $result->SizeBody = hexdec( substr( $header_data, 0, 4 ) );
    //--- get number of package
    $result->NumberPacket = hexdec( substr( $header_data, 4, 4 ) );
    //--- get flag
    $result->Flag = hexdec( substr( $header_data, 8, 1 ) );
    return $result;
    }
}

/**
 * constants for protocols
 */
class MTProtocolConsts
{
  //---authorization
  const WEB_CMD_AUTH_START = 'AUTH_START'; // begin of authorization on server
  const WEB_CMD_AUTH_ANSWER = 'AUTH_ANSWER'; // end of authorization on server
  //--- config
  const WEB_CMD_COMMON_GET = "COMMON_GET"; // get config
  //--- time
  const WEB_CMD_TIME_SERVER = "TIME_SERVER"; // get time of server
  const WEB_CMD_TIME_GET = "TIME_GET"; // get config of times
  //--- api
  const WEB_PREFIX_WEBAPI = "MT5WEBAPI%04x%04x"; //format of package for api
  const WEB_PACKET_FORMAT = "%04x%04x"; //format of packages second or more request
  const WEB_API_WORD = 'WebAPI';
  //---
  const WEB_PARAM_VERSION = "VERSION"; // version of authorization
  const WEB_PARAM_RETCODE = "RETCODE"; // code answer
  const WEB_PARAM_LOGIN = "LOGIN"; // login
  const WEB_PARAM_TYPE = "TYPE"; // type of connection, type of data, type of operation
  const WEB_PARAM_AGENT = "AGENT"; // agent name
  const WEB_PARAM_SRV_RAND = "SRV_RAND"; // server random string
  const WEB_PARAM_SRV_RAND_ANSWER = "SRV_RAND_ANSWER"; // answer on server random string
  const WEB_PARAM_CLI_RAND = "CLI_RAND"; // client's random string
  const WEB_PARAM_CLI_RAND_ANSWER = "CLI_RAND_ANSWER"; // answer to clients random string
  const WEB_PARAM_TIME = "TIME"; // time param
  const WEB_PARAM_TOTAL = "TOTAL"; // total
  const WEB_PARAM_INDEX = "INDEX"; // index
  const WEB_PARAM_GROUP = "GROUP"; // group
  const WEB_PARAM_SYMBOL = "SYMBOL"; // symbol
  const WEB_PARAM_NAME = "NAME"; // name
  const WEB_PARAM_COMPANY = "COMPANY"; // company
  const WEB_PARAM_LANGUAGE = "LANGUAGE"; // language (LANGID)
  const WEB_PARAM_COUNTRY = "COUNTRY"; // country
  const WEB_PARAM_CITY = "CITY"; // city
  const WEB_PARAM_STATE = "STATE"; // state
  const WEB_PARAM_ZIPCODE = "ZIPCODE"; // zipcode
  const WEB_PARAM_ADDRESS = "ADDRESS"; // address
  const WEB_PARAM_PHONE = "PHONE"; // phone
  const WEB_PARAM_EMAIL = "EMAIL"; // email
  const WEB_PARAM_ID = "ID"; // id
  const WEB_PARAM_STATUS = "STATUS"; // status
  const WEB_PARAM_COMMENT = "COMMENT"; // comment
  const WEB_PARAM_COLOR = "COLOR"; // color
  const WEB_PARAM_PASS_MAIN = "PASS_MAIN"; // main password
  const WEB_PARAM_PASS_INVESTOR = "PASS_INVESTOR"; // invest paswword
  const WEB_PARAM_PASS_API = "PASS_API"; // API password
  const WEB_PARAM_PASS_PHONE = "PASS_PHONE"; // phone password
  const WEB_PARAM_LEVERAGE = "LEVERAGE"; // leverage
  const WEB_PARAM_RIGHTS = "RIGHTS"; // rights
  const WEB_PARAM_BALANCE = "BALANCE"; // balance
  const WEB_PARAM_PASSWORD = "PASSWORD"; // password
  const WEB_PARAM_TICKET = "TICKET"; // ticket
  const WEB_PARAM_OFFSET = "OFFSET"; // offset for page requests
  const WEB_PARAM_FROM = "FROM"; // from time
  const WEB_PARAM_TO = "TO"; // to time
  const WEB_PARAM_TRANS_ID = "TRANS_ID"; // trans id
  const WEB_PARAM_SUBJECT = "SUBJECT"; // subject
  const WEB_PARAM_CATEGORY = "CATEGORY"; // category
  const WEB_PARAM_PRIORITY = "PRIORITY"; // priority
  const WEB_PARAM_BODYTEXT = "BODY_TEXT"; // big text
  const WEB_PARAM_CHECK_MARGIN = "CHECK_MARGIN"; // check margin
  //--- crypt
  const WEB_PARAM_CRYPT_METHOD = "CRYPT_METHOD"; // method of crypt
  const WEB_PARAM_CRYPT_RAND = "CRYPT_RAND"; // random string for crypt
  //--- group
  const WEB_CMD_GROUP_TOTAL = "GROUP_TOTAL"; // get count groups
  const WEB_CMD_GROUP_NEXT = "GROUP_NEXT"; // get next group
  const WEB_CMD_GROUP_GET = "GROUP_GET"; // get info about group
  const WEB_CMD_GROUP_ADD = "GROUP_ADD"; // group add
  const WEB_CMD_GROUP_DELETE = "GROUP_DELETE"; // group delete
  //--- symbols
  const WEB_CMD_SYMBOL_TOTAL = "SYMBOL_TOTAL"; // get count symbols
  const WEB_CMD_SYMBOL_NEXT = "SYMBOL_NEXT"; // get next symbol
  const WEB_CMD_SYMBOL_GET = "SYMBOL_GET"; // get info about symbol
  const WEB_CMD_SYMBOL_GET_GROUP = "SYMBOL_GET_GROUP"; // get info about symbol group
  const WEB_CMD_SYMBOL_ADD = "SYMBOL_ADD"; // symbol add
  const WEB_CMD_SYMBOL_DELETE = "SYMBOL_DELETE"; // symbol delete
  //--- user
  const WEB_CMD_USER_ADD = "USER_ADD"; // add new user
  const WEB_CMD_USER_UPDATE = "USER_UPDATE"; // update user
  const WEB_CMD_USER_DELETE = "USER_DELETE"; // delete user
  const WEB_CMD_USER_GET = "USER_GET"; // get user information
  const WEB_CMD_USER_PASS_CHECK = "USER_PASS_CHECK"; // user check
  const WEB_CMD_USER_PASS_CHANGE = "USER_PASS_CHANGE"; // password change
  const WEB_CMD_USER_ACCOUNT_GET = "USER_ACCOUNT_GET"; // account info get
  const WEB_CMD_USER_USER_LOGINS = "USER_LOGINS"; // users logins get
  //--- password type
  const WEB_VAL_USER_PASS_MAIN     = "MAIN";
  const WEB_VAL_USER_PASS_INVESTOR = "INVESTOR";
  const WEB_VAL_USER_PASS_API      = "API";
  //--- crypts
  const WEB_VAL_CRYPT_NONE      = "NONE";
  const WEB_VAL_CRYPT_AES256OFB = "AES256OFB";
  //--- trade command
  const WEB_CMD_USER_DEPOSIT_CHANGE = "USER_DEPOSIT_CHANGE"; // deposit change
  //--- work with order
  const WEB_CMD_ORDER_GET = "ORDER_GET"; // get order
  const WEB_CMD_ORDER_GET_TOTAL = "ORDER_GET_TOTAL"; // get count orders
  const WEB_CMD_ORDER_GET_PAGE = "ORDER_GET_PAGE"; // get order from history
  //--- work with position
  const WEB_CMD_POSITION_GET = "POSITION_GET"; // get position
  const WEB_CMD_POSITION_GET_TOTAL = "POSITION_GET_TOTAL"; // get count positions
  const WEB_CMD_POSITION_GET_PAGE = "POSITION_GET_PAGE"; // get positions
  //--- work with deal
  const WEB_CMD_DEAL_GET = "DEAL_GET"; // get deal
  const WEB_CMD_DEAL_GET_TOTAL = "DEAL_GET_TOTAL"; // get count deals
  const WEB_CMD_DEAL_GET_PAGE = "DEAL_GET_PAGE"; // get list of deals
  //--- work with history
  const WEB_CMD_HISTORY_GET = "HISTORY_GET"; // get history
  const WEB_CMD_HISTORY_GET_TOTAL = "HISTORY_GET_TOTAL"; // get count of history order
  const WEB_CMD_HISTORY_GET_PAGE = "HISTORY_GET_PAGE"; // get list of history
  //--- work with ticks
  const WEB_CMD_TICK_LAST = "TICK_LAST"; // get tick
  const WEB_CMD_TICK_LAST_GROUP = "TICK_LAST_GROUP"; // get tick by group name
  const WEB_CMD_TICK_STAT = "TICK_STAT"; //  tick stat
  //--- mail
  const WEB_CMD_MAIL_SEND = "MAIL_SEND";
  //--- news
  const WEB_CMD_NEWS_SEND = "NEWS_SEND";
  //--- ping
  const WEB_CMD_PING = "PING";
  //--- trade
  const WEB_CMD_TRADE_BALANCE = "TRADE_BALANCE";
  //--- server restart
  const WEB_CMD_SERVER_RESTART = "SERVER_RESTART";
}

?>