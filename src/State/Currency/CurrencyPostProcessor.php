<?php

declare(strict_types=1);

namespace App\State\Currency;

use App\Entity\Currency;
use App\ApiResource\CurrencyInput;

/**
 * A processor for post currency.
 */
class CurrencyPostProcessor
{
    // Methodes :

    /**
     * Returns a new currency filled with input datas.
     * @param \App\ApiResource\CurrencyInput $currencyInput the input datas.
     * @return \App\Entity\Currency the currency.
     */
    public function getEntity(CurrencyInput $currencyInput): Currency
    {
        return new Currency($currencyInput->getCode(), $currencyInput->getDecimals());
    }
}
