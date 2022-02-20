<?php


namespace AleeDhillon\MetaFive\Lib;


/**
 * class config symbol
 */
class MTConSymbol
{
    /**
     * name
     * @var string
     */
    public $Symbol;
    /**
     * hierarchical symbol path (including symbol name)
     * @var string
     */
    public $Path;
    /**
     * ISIN
     * @var string
     */
    public $ISIN;
    /**
     * local description
     * @var string
     */
    public $Description;
    /**
     * internation description
     * @var string
     */
    public $International;
    /**
     * basic symbol name
     * @var string
     */
    public $Basis;
    /**
     * source symbol name
     * @var string
     */
    public $Source;
    /**
     * symbol specification page URL
     * @var string
     */
    public $Page;
    /**
     * symbol base currency
     * @var string
     */
    public $CurrencyBase;
    /**
     * symbol base currency digits
     * @var int
     */
    public $CurrencyBaseDigits;
    /**
     * symbol profit currency
     * @var string
     */
    public $CurrencyProfit;
    /**
     * symbol profit currency digits
     * @var int
     */
    public $CurrencyProfitDigits;
    /**
     * symbol margin currency
     * @var string
     */
    public $CurrencyMargin;
    /**
     * symbol margin currency digits
     * @var int
     */
    public $CurrencyMarginDigits;
    /**
     * symbol color
     * @var int
     */
    public $Color;
    /**
     * symbol background color
     * @var int
     */
    public $ColorBackground;
    /**
     * symbol digits
     * @var int
     */
    public $Digits;
    /**
     * symbol digits derivation (1/10^digits & 10^digits)
     * @var double
     */
    public $Point;
    /**
     * Multiply
     * @var double
     */
    public $Multiply;
    /**
     * MTEnTickFlags
     * @var MTEnTickFlags
     */
    public $TickFlags;
    /**
     * Depth of Market depth (both legs)
     * @var int
     */
    public $TickBookDepth;
    /**
     * chart mode
     * @var MTEnChartMode
     */
    public $ChartMode;
    /**
     * filtration soft level
     * @var int
     */
    public $FilterSoft;
    /**
     * filtration soft level counter
     * @var int
     */
    public $FilterSoftTicks;
    /**
     * filtration hard level
     * @var int
     */
    public $FilterHard;
    /**
     * filtration hard level counter
     * @var int
     */
    public $FilterHardTicks;
    /**
     * filtration discard level
     * @var int
     */
    public $FilterDiscard;
    /**
     * spread max value
     * @var int
     */
    public $FilterSpreadMax;
    /**
     * spread min value
     * @var int
     */
    public $FilterSpreadMin;
    /**
     * gap level
     * @var int
     */
    public $FilterGap;
    /**
     * gap level ticks
     * @var int
     */
    public $FilterGapTicks;
    /**
     * @var MTEnTradeMode
     */
    public $TradeMode;
    /**
     * @var MTEnTradeFlags
     */
    public $TradeFlags;
    /**
     * @var MTEnCalcMode
     */
    public $CalcMode;
    /**
     * @var MTEnExecutionMode
     */
    public $ExecMode;
    /**
     * @var MTEnGTCMode
     */
    public $GTCMode;
    /**
     * @var MTEnFillingFlags
     */
    public $FillFlags;
    /**
     * @var MTEnExpirationFlags
     */
    public $ExpirFlags;
    /**
     * @var MTEnOrderFlags
     */
    public $OrderFlags;
    /**
     * symbol spread (0-floating)
     * @var int
     */
    public $Spread;
    /**
     * spread balance
     * @var int
     */
    public $SpreadBalance;
    /**
     * spread difference
     * @var int
     */
    public $SpreadDiff;
    /**
     * spread difference balance
     * @var int
     */
    public $SpreadDiffBalance;
    /**
     * tick value
     * @var double
     */
    public $TickValue;
    /**
     * tick size
     * @var double
     */
    public $TickSize;
    /**
     * contract size
     * @var double
     */
    public $ContractSize;
    /**
     * stops level
     * @var int
     */
    public $StopsLevel;
    /**
     * freeze level
     * @var int
     */
    public $FreezeLevel;
    /**
     * quotes timeout
     * @var int
     */
    public $QuotesTimeout;
    /**
     * minimal volume
     * @var int
     */
    public $VolumeMin;
    /**
     * minimal volume
     * @var int
     */
    public $VolumeMinExt;
    /**
     * maximal volume
     * @var int
     */
    public $VolumeMax;
    /**
     * maximal volume
     * @var int
     */
    public $VolumeMaxExt;
    /**
     * volume step
     * @var int
     */
    public $VolumeStep;
    /**
     * volume step
     * @var int
     */
    public $VolumeStepExt;
    /**
     * cumulative positions and orders limit
     * @var int
     */
    public $VolumeLimit;
    /**
     * cumulative positions and orders limit
     * @var int
     */
    public $VolumeLimitExt;
    /**
     * @var MTEnMarginFlags
     */
    public $MarginFlags;
    /**
     * initial margin
     * @var double
     */
    public $MarginInitial;
    /**
     * maintenance margin
     * @var double
     */
    public $MarginMaintenance;
    /**
     * orders and positions margin rates
     * @var array
     */
    public $MarginRateInitial;
    /**
     * orders and positions margin rates
     * @var array
     */
    public $MarginRateMaintenance;
    /**
     * orders and positions margin rates
     * @var double
     */
    public $MarginRateLiquidity;
    /**
     * hedged positions margin rate
     * @var double
     */
    public $MarginHedged;
    /**
     * margin currency rate
     * @var double
     */
    public $MarginRateCurrency;
    /**
     * long orders and positions margin rate
     * @deprecated should use MarginRateInitial and MarginRateMaintenance
     * @var double
     */
    public $MarginLong;
    /**
     * short orders and positions margin rate
     * @deprecated should use MarginRateInitial and MarginRateMaintenance
     * @var double
     */
    public $MarginShort;
    /**
     * limit orders and positions margin rate
     * @deprecated should use MarginRateInitial and MarginRateMaintenance
     * @var double
     */
    public $MarginLimit;
    /**
     * stop orders and positions margin rate
     * @deprecated should use MarginRateInitial and MarginRateMaintenance
     * @var double
     */
    public $MarginStop;
    /**
     * stop-limit orders and positions margin rate
     * @deprecated should use MarginRateInitial and MarginRateMaintenance
     * @var double
     */
    public $MarginStopLimit;
    /**
     * @deprecated should use MarginRateInitial and MarginRateMaintenance
     * @var MTEnSwapMode
     */
    public $SwapMode;
    /**
     * long positions swaps rate
     * @var double
     */
    public $SwapLong;
    /**
     * short positions swaps rate
     * @var double
     */
    public $SwapShort;
    /**
     * 3 time swaps day
     * @var int
     */
    public $Swap3Day;
    /**
     * trade start date
     * @var int
     */
    public $TimeStart;
    /**
     * trade end date
     * @var int
     */
    public $TimeExpiration;
    /**
     * quote sessions
     * @var array
     */
    public $SessionsQuotes;
    /**
     * trade sessions
     * @var array
     */
    public $SessionsTrades;
    /**
     * request execution flags
     * @var MTEnRequestFlags
     */
    public $REFlags;
    /**
     * request execution timeout
     * @var int
     */
    public $RETimeout;
    /**
     * instant execution check mode MTEnInstantMode
     * @var MTEnInstantMode
     */
    public $IECheckMode;
    /**
     * instant execution timeout
     * @var int
     */
    public $IETimeout;
    /**
     * instant execution profit slippage
     * @var int
     */
    public $IESlipProfit;
    /**
     * instant execution losing slippage
     * @var int
     */
    public $IESlipLosing;
    /**
     * instant execution max volume
     * @var int
     */
    public $IEVolumeMax;
    /**
     * instant execution max volume
     * @var int
     */
    public $IEVolumeMaxExt;
    /**
     * settle price (for futures)
     * @var double
     */
    public $PriceSettle;
    /**
     * price limit max (for futures)
     * @var double
     */
    public $PriceLimitMax;
    /**
     * price limit min (for futures)
     * @var double
     */
    public $PriceLimitMin;
    /**
     * option strike price value
     * @var double
     */
    public $PriceStrike;
    /**
     * @var MTEnOptionMode
     */
    public $OptionsMode;
    /**
     * @var double
     */
    public $FaceValue;
    /**
     * @var double
     */
    public $AccruedInterest;
    /**
     * @var MTEnSpliceType
     */
    public $SpliceType;
    /**
     * @var MTEnSpliceTimeType
     */
    public $SpliceTimeType;
    /**
     * @var int
     */
    public $SpliceTimeDays;

    /**
     * Create MTConSymbol with default values
     * @return MTConSymbol
     */
    public static function CreateDefault()
    {
        $symbol = new MTConSymbol();
        //---
        $symbol->CurrencyBase          = "USD";
        $symbol->CurrencyProfit        = "USD";
        $symbol->CurrencyMargin        = "USD";
        $symbol->Digits                = 4;
        $symbol->TickBookDepth         = 0;
        $symbol->TickFlags             = MTEnTickFlags::TICK_REALTIME;
        $symbol->FilterDiscard         = 500;
        $symbol->FilterSoftTicks       = 10;
        $symbol->FilterHardTicks       = 10;
        $symbol->FilterHard            = 500;
        $symbol->FilterSoft            = 100;
        $symbol->FilterSpreadMax       = 0;
        $symbol->FilterSpreadMin       = 0;
        $symbol->TradeMode             = MTEnTradeMode::TRADE_FULL;
        $symbol->TradeFlags            = MTEnTradeFlags::TRADE_FLAGS_DEFAULT;
        $symbol->Spread                = 0;
        $symbol->SpreadBalance         = 0;
        $symbol->TickValue             = 0;
        $symbol->TickSize              = 0;
        $symbol->ContractSize          = 100000;
        $symbol->GTCMode               = MTEnGTCMode::ORDERS_GTC;
        $symbol->CalcMode              = MTEnCalcMode::TRADE_MODE_FOREX;
        $symbol->QuotesTimeout         = 0;
        $symbol->PriceSettle           = 0;
        $symbol->PriceLimitMax         = 0;
        $symbol->PriceLimitMin         = 0;
        $symbol->TimeStart             = 0;
        $symbol->TimeExpiration        = 0;
        $symbol->SpreadDiff            = 0;
        $symbol->SpreadDiffBalance     = 0;
        $symbol->StopsLevel            = 5;
        $symbol->FreezeLevel           = 0;
        $symbol->ExecMode              = MTEnExecutionMode::EXECUTION_INSTANT;
        $symbol->FillFlags             = MTEnFillingFlags::FILL_FLAGS_FOK;
        $symbol->ExpirFlags            = MTEnExpirationFlags::TIME_FLAGS_ALL;
        $symbol->REFlags               = MTEnRequestFlags::REQUEST_FLAGS_NONE;
        $symbol->RETimeout             = 7;
        $symbol->IETimeout             = 7;
        $symbol->IESlipProfit          = 2;
        $symbol->IESlipLosing          = 2;
        $symbol->IEVolumeMax           = 0;
        $symbol->IECheckMode           = MTEnInstantMode::INSTANT_CHECK_NORMAL;
        $symbol->VolumeMin             = 0;
        $symbol->VolumeMax             = 100000;
        $symbol->VolumeMaxExt          = MTUtils::ToNewVolume($symbol->VolumeMax);
        $symbol->VolumeStep            = 10000;
        $symbol->VolumeStepExt         = MTUtils::ToNewVolume($symbol->VolumeStep);
        $symbol->VolumeLimit           = 0;
        $symbol->MarginFlags           = MTEnMarginFlags::MARGIN_FLAGS_NONE;
        $symbol->MarginInitial         = 0;
        $symbol->MarginMaintenance     = 0;
        $symbol->MarginRateInitial     = self::GetDefaultMarginRate();
        $symbol->MarginRateMaintenance = self::GetDefaultMarginRate();
        $symbol->MarginRateLiquidity   = 0;
        $symbol->MarginHedged          = 0;
        $symbol->MarginRateCurrency    = 0;
        //--- DEPRECATED
        $symbol->MarginLong            = 1;
        $symbol->MarginShort           = 1;
        $symbol->MarginLimit           = 0;
        $symbol->MarginStop            = 0;
        $symbol->MarginStopLimit       = 0;
        //---
        $symbol->SwapMode              = MTEnSwapMode::SWAP_DISABLED;
        $symbol->SwapLong              = 0;
        $symbol->SwapShort             = 0;
        $symbol->Swap3Day              = 3;
        $symbol->OrderFlags            = MTEnOrderFlags::ORDER_FLAGS_ALL;
        $symbol->OptionsMode           = MTEnOptionMode::OPTION_MODE_EUROPEAN_CALL;
        $symbol->PriceStrike           = 0;
        //---
        $symbol->FaceValue             = 0;
        $symbol->AccruedInterest       = 0;
        $symbol->SpliceType            = MTEnSpliceType::SPLICE_NONE;
        $symbol->SpliceTimeType        = MTEnSpliceTimeType::SPLICE_TIME_EXPIRATION;
        $symbol->SpliceTimeDays        = 0;
        //---
        return $symbol;
    }

    /**
     * Get dafault Margin rate
     * @return array
     */
    public static function GetDefaultMarginRate()
    {
        return array(MTEnMarginRateTypes::MARGIN_RATE_BUY             => 0.0,
                     MTEnMarginRateTypes::MARGIN_RATE_SELL            => 0.0,
                     MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT       => 0.0,
                     MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT      => 0.0,
                     MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP        => 0.0,
                     MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP       => 0.0,
                     MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT  => 0.0,
                     MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT => 0.0);
    }
}
