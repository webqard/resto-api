<?php

declare(strict_types=1);

namespace App\Entity\Association;

use App\Entity\Drink;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait that adds a 'Drink' field to an entity.
 */
trait DrinkAssociation
{
    // Properties :

    /**
     * @var \App\Entity\Drink the drink.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: Drink::class)
    ]
    private Drink $drink;
}
