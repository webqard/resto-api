<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The drink picture's translation entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["drink_picture_id", "locale_id"])
]
class DrinkPictureTranslation extends ProductPictureTranslation
{
    // Properties :

    /**
     * @var \App\Entity\DrinkPicture the picture.
     */
    #[
        ORM\JoinColumn(
            name: "drink_picture_id",
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(
            targetEntity: DrinkPicture::class,
            inversedBy: "translations"
        )
    ]
    private DrinkPicture $picture;


    // Magic methods :

    /**
     * @param \App\Entity\DrinkPicture $picture the drink's picture.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $alternative the alternative.
     * @param string|null $title the title.
     */
    public function __construct(
        DrinkPicture $picture,
        Locale $locale,
        ?string $alternative = null,
        ?string $title = null
    ) {
        parent::__construct(
            $locale,
            $alternative,
            $title
        );

        $this->picture = $picture;
    }
}
