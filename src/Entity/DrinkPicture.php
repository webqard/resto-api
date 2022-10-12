<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\DrinkAssociation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The drink's picture entity.
 */
#[ORM\Entity()]
class DrinkPicture extends ProductPicture
{
    // Traits :
    use DrinkAssociation;


    // Properties :

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\OneToMany(
            targetEntity: DrinkPictureTranslation::class,
            mappedBy: 'picture',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;


    // Magic methods :

    /**
     * @param \App\Entity\Drink $drink the drink.
     * @param string $source the source.
     * @param int $priority the priority.
     */
    public function __construct(
        Drink $drink,
        string $source,
        int $priority = 0
    ) {
        parent::__construct(
            $source,
            $priority
        );

        $this->drink = $drink;
        $this->translations = new ArrayCollection();
    }
}
