<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository for the post method of the Currency entity.
 */
class CurrencyPostRepository extends ServiceEntityRepository
{
    // Traits :

    use CurrencyRepository;


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
