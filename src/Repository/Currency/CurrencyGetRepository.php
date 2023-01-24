<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository for the get method of the Currency entity.
 */
class CurrencyGetRepository extends ServiceEntityRepository
{
    // Traits :

    use CurrencyRepository;


    // Methods :

    /**
     * @return \App\Entity\Currency|null a currency.
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?Currency
    {
        return parent::find($id, $lockMode, $lockVersion);
    }
}
