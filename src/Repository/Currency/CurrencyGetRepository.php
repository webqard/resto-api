<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository for the get method of the Currency entity.
 */
class CurrencyGetRepository extends ServiceEntityRepository
{
    // Traits :

    use CurrencyRepository;
}
