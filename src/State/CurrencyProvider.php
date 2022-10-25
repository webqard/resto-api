<?php

declare(strict_types=1);

namespace App\State;

use App\ApiResource\CurrencyOutput;
use App\Entity\Currency;

/**
 * A currency provider.
 */
class CurrencyProvider
{
    // Methodes :

    /**
     * Returns a currency to output.
     * @param \App\Entity\Currency $currency the currency.
     * @return \App\ApiResource\CurrencyOutput the currency to output.
     */
    public function provideCurrencyOutput(Currency $currency): CurrencyOutput
    {
        return new CurrencyOutput(
            $currency->getCode(),
            $currency->getDecimals()
        );
    }
}
