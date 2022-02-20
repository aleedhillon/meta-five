<?php


namespace AleeDhillon\MetaFive\Entities;


/**
 * Class Trade
 * @package AleeDhillon\MetaFive\Entities
 */
class Trade
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
    const DEAL_FIRST = Trade::DEAL_BUY;
    const DEAL_LAST  = Trade::DEAL_SO_COMPENSATION;

    protected $ticket;
    /**
     * @var
     */
    protected $login;

    /**
     * @var
     */
    protected $type;

    /**
     * @var
     */
    protected $amount;

    /**
     * @var
     */
    protected $comment;

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     * @return Trade
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Trade
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return Trade
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     * @return Trade
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param mixed $ticket
     * @return Trade
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
        return $this;
    }

}
