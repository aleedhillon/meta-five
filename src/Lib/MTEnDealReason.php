<?php


namespace AleeDhillon\MetaFive\Lib;


class MTEnDealReason
{
    const DEAL_REASON_CLIENT           = 0;  // deal placed manually
    const DEAL_REASON_EXPERT           = 1;  // deal placed by expert
    const DEAL_REASON_DEALER           = 2;  // deal placed by dealer
    const DEAL_REASON_SL               = 3;  // deal placed due SL
    const DEAL_REASON_TP               = 4;  // deal placed due TP
    const DEAL_REASON_SO               = 5;  // deal placed due Stop-Out
    const DEAL_REASON_ROLLOVER         = 6;  // deal placed due rollover
    const DEAL_REASON_EXTERNAL_CLIENT  = 7;  // deal placed from the external system by client
    const DEAL_REASON_VMARGIN          = 8;  // deal placed due variation margin
    const DEAL_REASON_GATEWAY          = 9;  // deal placed by gateway
    const DEAL_REASON_SIGNAL           = 10; // deal placed by signal service
    const DEAL_REASON_SETTLEMENT       = 11; // deal placed due settlement
    const DEAL_REASON_TRANSFER         = 12; // deal placed due position transfer
    const DEAL_REASON_SYNC             = 13; // deal placed due position synchronization
    const DEAL_REASON_EXTERNAL_SERVICE = 14; // deal placed from the external system due service issues
    const DEAL_REASON_MIGRATION        = 15; // deal placed due migration
    const DEAL_REASON_MOBILE           = 16; // deal placed manually by mobile terminal
    const DEAL_REASON_WEB              = 17; // deal placed manually by web terminal
    const DEAL_REASON_SPLIT            = 18; // deal placed due split
    //--- enumeration borders
    const DEAL_REASON_FIRST = MTEnDealReason::DEAL_REASON_CLIENT;
    const DEAL_REASON_LAST  = MTEnDealReason::DEAL_REASON_SPLIT;
}
