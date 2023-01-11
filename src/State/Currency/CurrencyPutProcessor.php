<?php

declare(strict_types=1);

namespace App\State\Currency;

use App\Entity\Currency;
use App\ApiResource\CurrencyInput;

/**
 * A processor for put currency.
 */
class CurrencyPutProcessor
{
    // Methodes :

    /**
     * Returns the currency filled with input datas.
     * @param \App\Entity\Currency $currency the currency to update.
     * @param \App\ApiResource\CurrencyInput $currencyInput the input datas.
     * @return \App\Entity\Currency the updated currency.
     */
    public function getEntity(Currency $currency, CurrencyInput $currencyInput): Currency
    {
        $currency->setCode($currencyInput->getCode());
        $currency->setDecimals($currencyInput->getDecimals());

        return $currency;
    }
}
