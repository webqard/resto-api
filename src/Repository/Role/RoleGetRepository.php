<?php

declare(strict_types=1);

namespace App\Repository\Role;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the get method of the Role entity.
 */
class RoleGetRepository extends ServiceEntityRepository
{
    // Magic methods :

    /**
     * The constructor.
     * @param \Doctrine\Persistence\ManagerRegistry $registry the registry manager.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }


    // Methods :

    /**
     * @return \App\Entity\Role|null a role.
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?Role
    {
        return parent::find($id, $lockMode, $lockVersion);
    }
}
