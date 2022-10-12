<?php

declare(strict_types=1);

namespace App\Entity\Property;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait that adds a 'setMenuOnly' field to an entity.
 */
trait SetMenuOnly
{
    // Properties :

    /**
     * @var bool if it is available only in set menu.
     */
    #[
        ORM\Column(
            options: [
                "default" => false
            ]
        )
    ]
    private bool $setMenuOnly;
}
