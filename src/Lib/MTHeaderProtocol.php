<?php


namespace AleeDhillon\MetaFive\Lib;


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

