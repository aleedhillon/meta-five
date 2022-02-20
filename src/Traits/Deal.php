<?php


namespace AleeDhillon\MetaFive\Traits;


use AleeDhillon\MetaFive\Lib\MTRetCode;
use AleeDhillon\MetaFive\Exceptions\ConnectionException;
use AleeDhillon\MetaFive\Exceptions\UserException;
use AleeDhillon\MetaFive\Lib\MTDealProtocol;
use AleeDhillon\MetaFive\Lib\MTDeal;

trait Deal
{
    /**
     * Get Deal By Ticket
     * @param $ticket
     * @return MTDeal
     * @throws ConnectionException
     * @throws UserException
     */
    public function dealGet($ticket)
    {
        $deal = null;
        if (!$this->isConnected()) {
            $conn = $this->connect();

            if ($conn != MTRetCode::MT_RET_OK) {
                throw new ConnectionException(MTRetCode::GetError($conn));
            }
        }
        $mt_order = new MTDealProtocol($this->m_connect);
        $result = $mt_order->DealGet($ticket, $deal);
        if ($result != MTRetCode::MT_RET_OK) {
            throw new UserException(MTRetCode::GetError($result));
        }
        return $deal;
    }

    /**
     * Get Deal total by login and date range
     * @param $login
     * @param $from
     * @param $to
     * @return int
     * @throws ConnectionException
     * @throws UserException
     */
    public function dealGetTotal($login, $from, $to)
    {
        $total = 0;
        if (!$this->isConnected()) {
            $conn = $this->connect();

            if ($conn != MTRetCode::MT_RET_OK) {
                throw new ConnectionException(MTRetCode::GetError($conn));
            }
        }
        $mt_order = new MTDealProtocol($this->m_connect);
        $result = $mt_order->DealGetTotal($login, $from, $to, $total);
        if ($result != MTRetCode::MT_RET_OK) {
            throw new UserException(MTRetCode::GetError($result));
        }
        return $total;
    }

    /**
     * Get Deals pagination by login and date range
     * @param $login
     * @param $from
     * @param $to
     * @param $offset
     * @param $total
     * @return MTDeal[]
     * @throws ConnectionException
     * @throws UserException
     */
    public function dealGetPaginate($login, $from, $to, $offset, $total)
    {
        $deals = [];
        if (!$this->isConnected()) {
            $conn = $this->connect();

            if ($conn != MTRetCode::MT_RET_OK) {
                throw new ConnectionException(MTRetCode::GetError($conn));
            }
        }
        $mt_order = new MTDealProtocol($this->m_connect);
        $result = $mt_order->DealGetPage($login, $from, $to, $offset, $total, $deals);
        if ($result != MTRetCode::MT_RET_OK) {
            throw new UserException(MTRetCode::GetError($result));
        }
        return $deals;
    }
}
