<?php


namespace AleeDhillon\MetaFive\Lib;


class MTEnUsersRights
{
    const USER_RIGHT_NONE             = 0x0000000000000000; // none
    const USER_RIGHT_ENABLED          = 0x0000000000000001; // client allowed to connect
    const USER_RIGHT_PASSWORD         = 0x0000000000000002; // client allowed to change password
    const USER_RIGHT_TRADE_DISABLED   = 0x0000000000000004; // client trading disabled
    const USER_RIGHT_INVESTOR         = 0x0000000000000008; // client is investor
    const USER_RIGHT_CONFIRMED        = 0x0000000000000010; // client certificate confirmed
    const USER_RIGHT_TRAILING         = 0x0000000000000020; // trailing stops are allowed
    const USER_RIGHT_EXPERT           = 0x0000000000000040; // expert advisors are allowed
    const USER_RIGHT_API              = 0x0000000000000080; // client API are allowed
    const USER_RIGHT_REPORTS          = 0x0000000000000100; // trade reports are allowed
    const USER_RIGHT_READONLY         = 0x0000000000000200; // client is readonly
    const USER_RIGHT_RESET_PASS       = 0x0000000000000400; // client must change password at next login
    const USER_RIGHT_OTP_ENABLED      = 0x0000000000000800; // client allowed to use one-time password
    const USER_RIGHT_CLIENT_CONFIRMED = 0x0000000000001000;
    const USER_RIGHT_VIRTUAL_HOSTING  = 0x0000000000002000; // client allowed to use sponsored by broker MetaTrader Virtual Hosting
    //--- enumeration borders
    const USER_RIGHT_DEFAULT = 0x0000000000000163; // USER_RIGHT_ENABLED | USER_RIGHT_PASSWORD | USER_RIGHT_TRAILING | USER_RIGHT_EXPERT | USER_RIGHT_REPORTS
    const USER_RIGHT_ALL     = 0x0000000000002FFF;
}
