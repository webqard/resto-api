<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;
use App\Repository\DeleteRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the delete method of the Locale entity.
 */
class LocaleDeleteRepository extends DeleteRepository
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
}
