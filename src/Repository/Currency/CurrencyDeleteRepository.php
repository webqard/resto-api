<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Entity\Currency;

/**
 * Repository for the delete method of the Currency entity.
 */
class CurrencyDeleteRepository extends CurrencyGetRepository
{
    // Methods :

    /**
     * Deletes a currency.
     * @param \App\Entity\Currency $currency the currency.
     */
    public function delete(Currency $currency): void
    {
        $this->_em->remove($currency);
        $this->_em->flush();
    }
}
