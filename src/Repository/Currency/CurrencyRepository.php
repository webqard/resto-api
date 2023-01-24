<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Entity\Currency;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Constructor for the Currency repositories.
 */
trait CurrencyRepository
{
    // Magic methods :

    /**
     * The constructor.
     * @param \Doctrine\Persistence\ManagerRegistry $registry the registry manager.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }
}
