<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Constructor for the User repositories.
 */
trait UserRepository
{
    // Magic methods :

    /**
     * The constructor.
     * @param \Doctrine\Persistence\ManagerRegistry $registry the registry manager.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
