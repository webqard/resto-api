<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Locale entity.
 */
class LocaleRepository extends ServiceEntityRepository
{
    // Magic methods :

    /**
     * The constructor.
     * @param \Doctrine\Persistence\ManagerRegistry $registry the registry manager.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Locale::class);
    }


    // Methods :

    /**
     * Deletes a locale.
     * @param \App\Entity\Locale $locale the locale.
     */
    public function delete(Locale $locale): void
    {
        $this->_em->remove($locale);
        $this->_em->flush();
    }
}
