<?php

declare(strict_types=1);

namespace App\Entity;

use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Priority;
use Doctrine\ORM\Mapping as ORM;

/**
 * A base entity for
 * product's picture.
 */
#[ORM\MappedSuperclass()]
abstract class ProductPicture
{
    // Traits :
    use IntId;
    use Priority;


    // Properties :

    /**
     * @var string the source of the picture (ex : \<img src="./path_to/file.jpg" />).
     */
    #[ORM\Column(length: 255)]
    protected string $source;


    // Magic methods :

    /**
     * @param string $source the source.
     * @param int $priority the priority.
     */
    public function __construct(
        string $source,
        int $priority = 0
    ) {
        $this->id = null;
        $this->source = $source;
        $this->priority = $priority;
    }
}
