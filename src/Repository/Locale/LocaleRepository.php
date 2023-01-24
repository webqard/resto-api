<?php

declare(strict_types=1);

namespace App\Repository\Locale;

use App\Entity\Locale;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Constructor for the Locale repositories.
 */
trait LocaleRepository
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
