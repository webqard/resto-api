<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Entity\Currency;

/**
 * Repository for the put method of the Currency entity.
 */
class CurrencyPutRepository extends CurrencyGetRepository
{
    // Methods :

    /**
     * Saves a currency.
     * @param \App\Entity\Currency $currency the currency.
     */
    public function save(Currency $currency): void
    {
        $this->_em->persist($currency);
        $this->_em->flush();
    }
}
