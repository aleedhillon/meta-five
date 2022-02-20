<?php
//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2001-2019, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
 class MTRetCode
   {
//--- successfully codes
   const MT_RET_OK                    =0;       // ok
   const MT_RET_OK_NONE               =1;       // ok; no data
//--- common errors
   const MT_RET_ERROR                 =2;       // Common error
   const MT_RET_ERR_PARAMS            =3;       // Invalid parameters
   const MT_RET_ERR_DATA              =4;       // Invalid data
   const MT_RET_ERR_DISK              =5;       // Disk error
   const MT_RET_ERR_MEM               =6;       // Memory error
   const MT_RET_ERR_NETWORK           =7;       // Network error
   const MT_RET_ERR_PERMISSIONS       =8;       // Not enough permissions
   const MT_RET_ERR_TIMEOUT           =9;       // Operation timeout
   const MT_RET_ERR_CONNECTION        =10;      // No connection
   const MT_RET_ERR_NOSERVICE         =11;      // Service is not available
   const MT_RET_ERR_FREQUENT          =12;      // Too frequent requests
   const MT_RET_ERR_NOTFOUND          =13;      // Not found
   const MT_RET_ERR_PARTIAL           =14;      // Partial error
   const MT_RET_ERR_SHUTDOWN          =15;      // Server shutdown in progress
   const MT_RET_ERR_CANCEL            =16;      // Operation has been canceled
   const MT_RET_ERR_DUPLICATE         =17;      // Duplicate data
//--- authentication retcodes
   const MT_RET_AUTH_CLIENT_INVALID   =1000;    // Invalid terminal type
   const MT_RET_AUTH_ACCOUNT_INVALID  =1001;    // Invalid account
   const MT_RET_AUTH_ACCOUNT_DISABLED =1002;    // Account disabled
   const MT_RET_AUTH_ADVANCED         =1003;    // Advanced authorization necessary
   const MT_RET_AUTH_CERTIFICATE      =1004;    // Certificate required
   const MT_RET_AUTH_CERTIFICATE_BAD  =1005;    // Invalid certificate
   const MT_RET_AUTH_NOTCONFIRMED     =1006;    // Certificate is not confirmed
   const MT_RET_AUTH_SERVER_INTERNAL  =1007;    // Attempt to connect to non-access server
   const MT_RET_AUTH_SERVER_BAD       =1008;    // Server isn't authenticated
   const MT_RET_AUTH_UPDATE_ONLY      =1009;    // Only updates available
   const MT_RET_AUTH_CLIENT_OLD       =1010;    // Client has old version
   const MT_RET_AUTH_MANAGER_NOCONFIG =1011;    // Manager account doesn't have manager config
   const MT_RET_AUTH_MANAGER_IPBLOCK  =1012;    // IP address unallowed for manager
   const MT_RET_AUTH_GROUP_INVALID    =1013;    // Group is not initialized (server restart neccesary)
   const MT_RET_AUTH_CA_DISABLED      =1014;    // Certificate generation disabled
   const MT_RET_AUTH_INVALID_ID       =1015;    // Invalid or disabled server id [check server's id]
   const MT_RET_AUTH_INVALID_IP       =1016;    // Unallowed address [check server's ip address]
   const MT_RET_AUTH_INVALID_TYPE     =1017;    // Invalid server type [check server's id and type]
   const MT_RET_AUTH_SERVER_BUSY      =1018;    // Server is busy
   const MT_RET_AUTH_SERVER_CERT      =1019;    // Invalid server certificate
   const MT_RET_AUTH_ACCOUNT_UNKNOWN  =1020;    // Unknown account
   const MT_RET_AUTH_SERVER_OLD       =1021;    // Old server version
   const MT_RET_AUTH_SERVER_LIMIT     =1022;    // Server cannot be connected due to license limitation
   const MT_RET_AUTH_MOBILE_DISABLED  =1023;    // Mobile connection aren't allowed in server license
//--- config management retcodes
   const MT_RET_CFG_LAST_ADMIN        =2000;    // Last admin config deleting
   const MT_RET_CFG_LAST_ADMIN_GROUP  =2001;    // Last admin group cannot be deleted
   const MT_RET_CFG_NOT_EMPTY         =2003;    // Accounts or trades in group
   const MT_RET_CFG_INVALID_RANGE     =2004;    // Invalid accounts or trades ranges
   const MT_RET_CFG_NOT_MANAGER_LOGIN =2005;    // Manager account is not from manager group
   const MT_RET_CFG_BUILTIN           =2006;    // Built-in protected config
   const MT_RET_CFG_DUPLICATE         =2007;    // Configuration duplicate
   const MT_RET_CFG_LIMIT_REACHED     =2008;    // Configuration limit reached
   const MT_RET_CFG_NO_ACCESS_TO_MAIN =2009;    // Invalid network configuration
   const MT_RET_CFG_DEALER_ID_EXIST   =2010;    // Dealer with same ID exists
   const MT_RET_CFG_BIND_ADDR_EXIST   =2011;    // Bind address already exists
   const MT_RET_CFG_WORKING_TRADE     =2012;    // Attempt to delete working trade server
//--- client management retcodes
   const MT_RET_USR_LAST_ADMIN        =3001;    // Last admin account deleting
   const MT_RET_USR_LOGIN_EXHAUSTED   =3002;    // Logins range exhausted
   const MT_RET_USR_LOGIN_PROHIBITED  =3003;    // Login reserved at another server
   const MT_RET_USR_LOGIN_EXIST       =3004;    // Account already exists
   const MT_RET_USR_SUICIDE           =3005;    // Attempt of self-deletion
   const MT_RET_USR_INVALID_PASSWORD  =3006;    // Invalid account password
   const MT_RET_USR_LIMIT_REACHED     =3007;    // Users limit reached
   const MT_RET_USR_HAS_TRADES        =3008;    // Account has open trades
   const MT_RET_USR_DIFFERENT_SERVERS =3009;    // Attempt to move account to different server
   const MT_RET_USR_DIFFERENT_CURRENCY=3010;    // Attempt to move account to different currency group
   const MT_RET_USR_IMPORT_BALANCE    =3011;    // Account balance import error
   const MT_RET_USR_IMPORT_GROUP      =3012;    // Account import with invalid group
//--- trades management retcodes
   const MT_RET_TRADE_LIMIT_REACHED   =4001;    // Orders or deals limit reached
   const MT_RET_TRADE_ORDER_EXIST     =4002;    // Order already exists
   const MT_RET_TRADE_ORDER_EXHAUSTED =4003;    // Orders range exhausted
   const MT_RET_TRADE_DEAL_EXHAUSTED  =4004;    // Deals range exhausted
   const MT_RET_TRADE_MAX_MONEY       =4005;    // Money limit reached
//--- report generation retcodes
   const MT_RET_REPORT_SNAPSHOT       =5001;    // Base snapshot error
   const MT_RET_REPORT_NOTSUPPORTED   =5002;    // Method doesn't support for this report
   const MT_RET_REPORT_NODATA         =5003;    // No report data
   const MT_RET_REPORT_TEMPLATE_BAD   =5004;    // Bad template
   const MT_RET_REPORT_TEMPLATE_END   =5005;    // End of template (template success processed)
   const MT_RET_REPORT_INVALID_ROW    =5006;    // Invalid row size
   const MT_RET_REPORT_LIMIT_REPEAT   =5007;    // Tag repeat limit reached
   const MT_RET_REPORT_LIMIT_REPORT   =5008;    // Report size limit reached
//--- price history reports retcodes
   const MT_RET_HST_SYMBOL_NOTFOUND   =6001;    // Symbol not found; try to restart history server
//--- trade request retcodes
   const MT_RET_REQUEST_INWAY         =10001;   // Request on the way
   const MT_RET_REQUEST_ACCEPTED      =10002;   // Request accepted
   const MT_RET_REQUEST_PROCESS       =10003;   // Request processed
   const MT_RET_REQUEST_REQUOTE       =10004;   // Request Requoted
   const MT_RET_REQUEST_PRICES        =10005;   // Request Prices
   const MT_RET_REQUEST_REJECT        =10006;   // Request rejected
   const MT_RET_REQUEST_CANCEL        =10007;   // Request canceled
   const MT_RET_REQUEST_PLACED        =10008;   // Order from requestplaced
   const MT_RET_REQUEST_DONE          =10009;   // Request executed
   const MT_RET_REQUEST_DONE_PARTIAL  =10010;   // Request executed partially
   const MT_RET_REQUEST_ERROR         =10011;   // Request common error
   const MT_RET_REQUEST_TIMEOUT       =10012;   // Request timeout
   const MT_RET_REQUEST_INVALID       =10013;   // Invalid request
   const MT_RET_REQUEST_INVALID_VOLUME=10014;   // Invalid volume
   const MT_RET_REQUEST_INVALID_PRICE =10015;   // Invalid price
   const MT_RET_REQUEST_INVALID_STOPS =10016;   // Invalid stops or price
   const MT_RET_REQUEST_TRADE_DISABLED=10017;   // Trade disabled
   const MT_RET_REQUEST_MARKET_CLOSED =10018;   // Market closed
   const MT_RET_REQUEST_NO_MONEY      =10019;   // Not enough money
   const MT_RET_REQUEST_PRICE_CHANGED =10020;   // Price changed
   const MT_RET_REQUEST_PRICE_OFF     =10021;   // No prices
   const MT_RET_REQUEST_INVALID_EXP   =10022;   // Invalid order expiration
   const MT_RET_REQUEST_ORDER_CHANGED =10023;   // Order has been changed already
   const MT_RET_REQUEST_TOO_MANY      =10024;   // Too many trade requests
   const MT_RET_REQUEST_NO_CHANGES    =10025;   // Request doesn't contain changes
   const MT_RET_REQUEST_AT_DISABLED_SERVER=10026; // AutoTrading disabled by server
   const MT_RET_REQUEST_AT_DISABLED_CLIENT=10027; // AutoTrading disabled by client
   const MT_RET_REQUEST_LOCKED        =10028;     // Request locked by dealer
   const MT_RET_REQUEST_FROZED        =10029;     // Order or position frozen
   const MT_RET_REQUEST_INVALID_FILL  =10030;     // Unsupported filling mode
   const MT_RET_REQUEST_CONNECTION    =10031;     // No connection
   const MT_RET_REQUEST_ONLY_REAL     =10032;     // Allowed for real accounts only
   const MT_RET_REQUEST_LIMIT_ORDERS  =10033;     // Orders limit reached
   const MT_RET_REQUEST_LIMIT_VOLUME  =10034;     // Volume limit reached
//--- dealer retcodes
   const MT_RET_REQUEST_RETURN        =11000;     // Request returned in queue
   const MT_RET_REQUEST_DONE_CANCEL   =11001;     // Request partially filled; remainder has been canceled
   const MT_RET_REQUEST_REQUOTE_RETURN=11002;     // Request requoted and returned in queue with new prices
//--- API retcodes
   const MT_RET_ERR_NOTIMPLEMENT      =12000;     // Not implement yet
   const MT_RET_ERR_NOTMAIN           =12001;     // Operation must be performed on main server
   const MT_RET_ERR_NOTSUPPORTED      =12002;     // Command doesn't supported
   const MT_RET_ERR_DEADLOCK          =12003;     // Operation canceled due possible deadlock
   const MT_RET_ERR_LOCKED            =12004;     // Operation on locked entity
    /**
     * Get error string by code
     * @static
     * @param MTRetCode $error_code
     * @return string error
     */
   public static function GetError($error_code)
     {
       switch($error_code)
       {
       case MTRetCode::MT_RET_OK                    : return'ok';
       case MTRetCode::MT_RET_OK_NONE               : return 'ok; no data';
       case MTRetCode::MT_RET_ERROR                 : return 'Common error';
       case MTRetCode::MT_RET_ERR_PARAMS            : return 'Invalid parameters';
       case MTRetCode::MT_RET_ERR_DATA              : return 'Invalid data';
       case MTRetCode::MT_RET_ERR_DISK              : return 'Disk error';
       case MTRetCode::MT_RET_ERR_MEM               : return 'Memory error';
       case MTRetCode::MT_RET_ERR_NETWORK           : return 'Network error';
       case MTRetCode::MT_RET_ERR_PERMISSIONS       : return 'Not enough permissions';
       case MTRetCode::MT_RET_ERR_TIMEOUT           : return 'Operation timeout';
       case MTRetCode::MT_RET_ERR_CONNECTION        : return 'No connection';
       case MTRetCode::MT_RET_ERR_NOSERVICE         : return 'Service is not available';
       case MTRetCode::MT_RET_ERR_FREQUENT          : return 'Too frequent requests';
       case MTRetCode::MT_RET_ERR_NOTFOUND          : return 'Not found';
       case MTRetCode::MT_RET_ERR_PARTIAL           : return 'Partial error';
       case MTRetCode::MT_RET_ERR_SHUTDOWN          : return 'Server shutdown in progress';
       case MTRetCode::MT_RET_ERR_CANCEL            : return 'Operation has been canceled';
       case MTRetCode::MT_RET_ERR_DUPLICATE         : return 'Duplicate data';
        //---
       case MTRetCode::MT_RET_AUTH_CLIENT_INVALID   : return 'Invalid terminal type';
       case MTRetCode::MT_RET_AUTH_ACCOUNT_INVALID  : return 'Invalid account';
       case MTRetCode::MT_RET_AUTH_ACCOUNT_DISABLED : return 'Account disabled';
       case MTRetCode::MT_RET_AUTH_ADVANCED         : return 'Advanced authorization necessary';
       case MTRetCode::MT_RET_AUTH_CERTIFICATE      : return 'Certificate required';
       case MTRetCode::MT_RET_AUTH_CERTIFICATE_BAD  : return 'Invalid certificate';
       case MTRetCode::MT_RET_AUTH_NOTCONFIRMED     : return 'Certificate is not confirmed';
       case MTRetCode::MT_RET_AUTH_SERVER_INTERNAL  : return 'Attempt to connect to non-access server';
       case MTRetCode::MT_RET_AUTH_SERVER_BAD       : return 'Server is not authenticated';
       case MTRetCode::MT_RET_AUTH_UPDATE_ONLY      : return 'Only updates available';
       case MTRetCode::MT_RET_AUTH_CLIENT_OLD       : return 'Client has old version';
       case MTRetCode::MT_RET_AUTH_MANAGER_NOCONFIG : return 'Manager account does not have manager config';
       case MTRetCode::MT_RET_AUTH_MANAGER_IPBLOCK  : return 'IP address unallowed for manager';
       case MTRetCode::MT_RET_AUTH_GROUP_INVALID    : return 'Group is not initialized (server restart neccesary)';
       case MTRetCode::MT_RET_AUTH_CA_DISABLED      : return 'Certificate generation disabled';
       case MTRetCode::MT_RET_AUTH_INVALID_ID       : return 'Invalid or disabled server id [check server\'s id]';
       case MTRetCode::MT_RET_AUTH_INVALID_IP       : return 'Unallowed address [check server\'s ip address]';
       case MTRetCode::MT_RET_AUTH_INVALID_TYPE     : return 'Invalid server type [check server\'s id and type]';
       case MTRetCode::MT_RET_AUTH_SERVER_BUSY      : return 'Server is busy';
       case MTRetCode::MT_RET_AUTH_SERVER_CERT      : return 'Invalid server certificate';
       case MTRetCode::MT_RET_AUTH_ACCOUNT_UNKNOWN  : return 'Unknown account';
       case MTRetCode::MT_RET_AUTH_SERVER_OLD       : return 'Old server version';
       case MTRetCode::MT_RET_AUTH_SERVER_LIMIT     : return 'Server cannot be connected due to license limitation';
       case MTRetCode::MT_RET_AUTH_MOBILE_DISABLED  : return 'Mobile connection aren\'t allowed in server license';
       //---
       case MTRetCode::MT_RET_CFG_LAST_ADMIN        : return 'Last admin config deleting';
       case MTRetCode::MT_RET_CFG_LAST_ADMIN_GROUP  : return 'Last admin group cannot be deleted';
       case MTRetCode::MT_RET_CFG_NOT_EMPTY         : return 'Accounts or trades in group';
       case MTRetCode::MT_RET_CFG_INVALID_RANGE     : return 'Invalid accounts or trades ranges';
       case MTRetCode::MT_RET_CFG_NOT_MANAGER_LOGIN : return 'Manager account is not from manager group';
       case MTRetCode::MT_RET_CFG_BUILTIN           : return 'Built-in protected config';
       case MTRetCode::MT_RET_CFG_DUPLICATE         : return 'Configuration duplicate';
       case MTRetCode::MT_RET_CFG_LIMIT_REACHED     : return 'Configuration limit reached';
       case MTRetCode::MT_RET_CFG_NO_ACCESS_TO_MAIN : return 'Invalid network configuration';
       case MTRetCode::MT_RET_CFG_DEALER_ID_EXIST   : return 'Dealer with same ID exists';
       case MTRetCode::MT_RET_CFG_BIND_ADDR_EXIST   : return 'Bind address already exists';
       case MTRetCode::MT_RET_CFG_WORKING_TRADE     : return 'Attempt to delete working trade server';
       //---
       case MTRetCode::MT_RET_USR_LAST_ADMIN        : return 'Last admin account deleting';
       case MTRetCode::MT_RET_USR_LOGIN_EXHAUSTED   : return 'Logins range exhausted';
       case MTRetCode::MT_RET_USR_LOGIN_PROHIBITED  : return 'Login reserved at another server';
       case MTRetCode::MT_RET_USR_LOGIN_EXIST       : return 'Account already exists';
       case MTRetCode::MT_RET_USR_SUICIDE           : return 'Attempt of self-deletion';
       case MTRetCode::MT_RET_USR_INVALID_PASSWORD  : return 'Invalid account password';
       case MTRetCode::MT_RET_USR_LIMIT_REACHED     : return 'Users limit reached';
       case MTRetCode::MT_RET_USR_HAS_TRADES        : return 'Account has open trades';
       case MTRetCode::MT_RET_USR_DIFFERENT_SERVERS : return 'Attempt to move account to different server';
       case MTRetCode::MT_RET_USR_DIFFERENT_CURRENCY: return 'Attempt to move account to different currency group';
       case MTRetCode::MT_RET_USR_IMPORT_BALANCE    : return 'Account balance import error';
       case MTRetCode::MT_RET_USR_IMPORT_GROUP      : return 'Account import with invalid group';
       //---
       case MTRetCode::MT_RET_TRADE_LIMIT_REACHED   : return 'Orders or deals limit reached';
       case MTRetCode::MT_RET_TRADE_ORDER_EXIST     : return 'Order already exists';
       case MTRetCode::MT_RET_TRADE_ORDER_EXHAUSTED : return 'Orders range exhausted';
       case MTRetCode::MT_RET_TRADE_DEAL_EXHAUSTED  : return 'Deals range exhausted';
       case MTRetCode::MT_RET_TRADE_MAX_MONEY       : return 'Money limit reached';
      //---
       case MTRetCode::MT_RET_REPORT_SNAPSHOT       : return 'Base snapshot error';
       case MTRetCode::MT_RET_REPORT_NOTSUPPORTED   : return 'Method doesn\'t support for this report';
       case MTRetCode::MT_RET_REPORT_NODATA         : return 'No report data';
       case MTRetCode::MT_RET_REPORT_TEMPLATE_BAD   : return 'Bad template';
       case MTRetCode::MT_RET_REPORT_TEMPLATE_END   : return 'End of template (template success processed)';
       case MTRetCode::MT_RET_REPORT_INVALID_ROW    : return 'Invalid row size';
       case MTRetCode::MT_RET_REPORT_LIMIT_REPEAT   : return 'Tag repeat limit reached';
       case MTRetCode::MT_RET_REPORT_LIMIT_REPORT   : return 'Report size limit reached';
       //---
       case MTRetCode::MT_RET_HST_SYMBOL_NOTFOUND   : return 'Symbol not found; try to restart history server';
       //---
       case MTRetCode::MT_RET_REQUEST_INWAY         : return 'Request on the way';
       case MTRetCode::MT_RET_REQUEST_ACCEPTED      : return 'Request accepted';
       case MTRetCode::MT_RET_REQUEST_PROCESS       : return 'Request processed';
       case MTRetCode::MT_RET_REQUEST_REQUOTE       : return 'Request Requoted';
       case MTRetCode::MT_RET_REQUEST_PRICES        : return 'Request Prices';
       case MTRetCode::MT_RET_REQUEST_REJECT        : return 'Request rejected';
       case MTRetCode::MT_RET_REQUEST_CANCEL        : return 'Request canceled';
       case MTRetCode::MT_RET_REQUEST_PLACED        : return 'Order from requestplaced';
       case MTRetCode::MT_RET_REQUEST_DONE          : return 'Request executed';
       case MTRetCode::MT_RET_REQUEST_DONE_PARTIAL  : return 'Request executed partially';
       case MTRetCode::MT_RET_REQUEST_ERROR         : return 'Request common error';
       case MTRetCode::MT_RET_REQUEST_TIMEOUT       : return 'Request timeout';
       case MTRetCode::MT_RET_REQUEST_INVALID       : return 'Invalid request';
       case MTRetCode::MT_RET_REQUEST_INVALID_VOLUME: return 'Invalid volume';
       case MTRetCode::MT_RET_REQUEST_INVALID_PRICE : return 'Invalid price';
       case MTRetCode::MT_RET_REQUEST_INVALID_STOPS : return 'Invalid stops or price';
       case MTRetCode::MT_RET_REQUEST_TRADE_DISABLED: return 'Trade disabled';
       case MTRetCode::MT_RET_REQUEST_MARKET_CLOSED : return 'Market closed';
       case MTRetCode::MT_RET_REQUEST_NO_MONEY      : return 'Not enough money';
       case MTRetCode::MT_RET_REQUEST_PRICE_CHANGED : return 'Price changed';
       case MTRetCode::MT_RET_REQUEST_PRICE_OFF     : return 'No prices';
       case MTRetCode::MT_RET_REQUEST_INVALID_EXP   : return 'Invalid order expiration';
       case MTRetCode::MT_RET_REQUEST_ORDER_CHANGED : return 'Order has been changed already';
       case MTRetCode::MT_RET_REQUEST_TOO_MANY      : return 'Too many trade requests';
       case MTRetCode::MT_RET_REQUEST_NO_CHANGES    : return 'Request doesn\'t contain changes';
       case MTRetCode::MT_RET_REQUEST_AT_DISABLED_SERVER: return 'AutoTrading disabled by server';
       case MTRetCode::MT_RET_REQUEST_AT_DISABLED_CLIENT: return 'AutoTrading disabled by client';
       case MTRetCode::MT_RET_REQUEST_LOCKED        : return 'Request locked by dealer';
       case MTRetCode::MT_RET_REQUEST_FROZED        : return 'Order or position frozen';
       case MTRetCode::MT_RET_REQUEST_INVALID_FILL  : return 'Unsupported filling mode';
       case MTRetCode::MT_RET_REQUEST_CONNECTION    : return 'No connection';
       case MTRetCode::MT_RET_REQUEST_ONLY_REAL     : return 'Allowed for real accounts only';
       case MTRetCode::MT_RET_REQUEST_LIMIT_ORDERS  : return 'Orders limit reached';
       case MTRetCode::MT_RET_REQUEST_LIMIT_VOLUME  : return 'Volume limit reached';
       //---
       case MTRetCode::MT_RET_REQUEST_RETURN        : return 'Request returned in queue';
       case MTRetCode::MT_RET_REQUEST_DONE_CANCEL   : return 'Request partially filled; remainder has been canceled';
       case MTRetCode::MT_RET_REQUEST_REQUOTE_RETURN: return 'Request requoted and returned in queue with new prices';
       //---
       case MTRetCode::MT_RET_ERR_NOTIMPLEMENT      : return 'Not implement yet';
       case MTRetCode::MT_RET_ERR_NOTMAIN           : return 'Operation must be performed on main server';
       case MTRetCode::MT_RET_ERR_NOTSUPPORTED      : return 'Command doesn\'t supported';
       case MTRetCode::MT_RET_ERR_DEADLOCK          : return 'Operation canceled due possible deadlock';
       case MTRetCode::MT_RET_ERR_LOCKED            : return 'Operation on locked entity';
       }
       return "unknown error";
     }
   }
?>