<?php

declare(strict_types=1);

namespace App\Entity\Association;

use App\Entity\Locale;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait that adds a 'Locale' field to an entity.
 */
trait LocaleAssociation
{
    // Properties :

    /**
     * @var \App\Entity\Locale the locale.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: Locale::class)
    ]
    protected Locale $locale;
}
