<?php


namespace AleeDhillon\MetaFive\Lib;


/**
 * types of transactions
 */
class MTEnDealAction
{
    const DEAL_BUY                = 0; // buy
    const DEAL_SELL               = 1; // sell
    const DEAL_BALANCE            = 2; // deposit operation
    const DEAL_CREDIT             = 3; // credit operation
    const DEAL_CHARGE             = 4; // additional charges
    const DEAL_CORRECTION         = 5; // correction deals
    const DEAL_BONUS              = 6; // bouns
    const DEAL_COMMISSION         = 7; // commission
    const DEAL_COMMISSION_DAILY   = 8; // daily commission
    const DEAL_COMMISSION_MONTHLY = 9; // monthly commission
    const DEAL_AGENT_DAILY        = 10; // daily agent commission
    const DEAL_AGENT_MONTHLY      = 11; // monthly agent commission
    const DEAL_INTERESTRATE       = 12; // interest rate charges
    const DEAL_BUY_CANCELED       = 13; // canceled buy deal
    const DEAL_SELL_CANCELED      = 14; // canceled sell deal
    const DEAL_DIVIDEND           = 15; // dividend
    const DEAL_DIVIDEND_FRANKED   = 16; // franked dividend
    const DEAL_TAX                = 17; // taxes
    const DEAL_AGENT              = 18; // instant agent commission
    const DEAL_SO_COMPENSATION    = 19; // negative balance compensation after stop-out
    //--- enumeration borders
    const DEAL_FIRST = MTEnDealAction::DEAL_BUY;
    const DEAL_LAST  = MTEnDealAction::DEAL_SO_COMPENSATION;
}
