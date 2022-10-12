<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\SetMenuAssociation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu's picture entity.
 */
#[ORM\Entity()]
class SetMenuPicture extends ProductPicture
{
    // Traits :
    use SetMenuAssociation;


    // Properties :

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\OneToMany(
            targetEntity: SetMenuPictureTranslation::class,
            mappedBy: 'picture',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;


    // Magic methods :

    /**
     * @param \App\Entity\SetMenu $setMenu the set menu.
     * @param string $source the source.
     * @param int $priority the priority.
     */
    public function __construct(
        SetMenu $setMenu,
        string $source,
        int $priority = 0
    ) {
        parent::__construct(
            $source,
            $priority
        );

        $this->setMenu = $setMenu;
        $this->translations = new ArrayCollection();
    }
}
