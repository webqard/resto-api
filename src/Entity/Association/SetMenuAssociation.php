<?php

declare(strict_types=1);

namespace App\Entity\Association;

use App\Entity\SetMenu;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait that adds a 'SetMenu' field to an entity.
 */
trait SetMenuAssociation
{
    // Properties :

    /**
     * @var \App\Entity\SetMenu the set menu.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: SetMenu::class)
    ]
    protected SetMenu $setMenu;
}
