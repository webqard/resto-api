<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the put method of the Currency entity.
 */
class CurrencyPutRepository extends ServiceEntityRepository
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
