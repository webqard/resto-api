<?php

declare(strict_types=1);

namespace App\Entity;

use CyrilVerloop\DoctrineEntities\Available;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Priority;
use Doctrine\ORM\Mapping as ORM;

/**
 * A base entity for
 * courses, drinks and menus.
 */
#[ORM\MappedSuperclass()]
abstract class Product extends IntId
{
    // Traits :
    use Available;
    use Priority;


    // Magic methods :

    /**
     * @param bool $available the availability.
     * @param int $priority the priority.
     */
    public function __construct(
        bool $available = true,
        int $priority = 0
    ) {
        parent::__construct();

        $this->available = $available;
        $this->priority = $priority;
    }
}
