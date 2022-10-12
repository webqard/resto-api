<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu entity.
 */
#[ORM\Entity()]
class SetMenu extends Product
{
    // Properties :

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\OneToMany(
            targetEntity: SetMenuTranslation::class,
            mappedBy: 'setMenu',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;

    /**
     * @var \Doctrine\Common\Collections\Collection the prices.
     */
    #[
        ORM\OneToMany(
            targetEntity: SetMenuPrice::class,
            mappedBy: 'setMenu',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $prices;

    /**
     * @var \Doctrine\Common\Collections\Collection the pictures.
     */
    #[
        ORM\OneToMany(
            targetEntity: SetMenuPicture::class,
            mappedBy: 'setMenu',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $pictures;


    // Magic methods :

    public function __construct(
        bool $available = true,
        int $priority = 0
    ) {
        parent::__construct(
            $available,
            $priority
        );

        $this->translations = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }
}
