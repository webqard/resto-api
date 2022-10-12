<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\SetMenuAssociation;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Priority;
use Doctrine\ORM\Mapping as ORM;

/**
 * The product in a set menu.
 */
#[ORM\MappedSuperclass()]
abstract class SetMenuProduct extends IntId
{
    // Traits :
    use Priority;
    use SetMenuAssociation;


    // Properties :

    /**
     * @var \App\Entity\SetMenuCategory|null the set menu category.
     */
    #[
        ORM\JoinColumn(onDelete: "SET NULL"),
        ORM\ManyToOne(
            targetEntity: SetMenuCategory::class,
            fetch: "EXTRA_LAZY"
        )
    ]
    protected ?SetMenuCategory $setMenuCategory;


    // Magic methods :

    /**
     * @param \App\Entity\SetMenu $setMenu the set menu.
     * @param \App\Entity\SetMenuCategory|null $setMenuCategory the set menu category.
     * @param int $priority the priority.
     */
    public function __construct(
        SetMenu $setMenu,
        ?SetMenuCategory $setMenuCategory = null,
        int $priority = 0
    ) {
        parent::__construct();

        $this->setMenu = $setMenu;
        $this->setMenuCategory = $setMenuCategory;
        $this->priority = $priority;
    }
}
