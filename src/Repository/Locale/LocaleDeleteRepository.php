<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the delete method of the Locale entity.
 */
class LocaleDeleteRepository extends ServiceEntityRepository
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
     * @param object $locale the locale.
     */
    public function delete(object $locale): void
    {
        $this->_em->remove($locale);
        $this->_em->flush();
    }
}
