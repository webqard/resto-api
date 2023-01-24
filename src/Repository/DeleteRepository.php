<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository for the delete method.
 */
abstract class DeleteRepository extends ServiceEntityRepository
{
    // Methods :

    /**
     * Deletes an entity.
     * @param object $entity the entity.
     */
    public function delete(object $entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }
}
