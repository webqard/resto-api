<?php

declare(strict_types=1);

namespace App\Repository\Currency;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the delete method of the Currency entity.
 */
class CurrencyDeleteRepository extends ServiceEntityRepository
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
     * Deletes a currency.
     * @param object $currency the currency.
     */
    public function delete(object $currency): void
    {
        $this->_em->remove($currency);
        $this->_em->flush();
    }
}
