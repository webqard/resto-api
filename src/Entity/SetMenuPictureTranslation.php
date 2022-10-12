<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu picture's translation entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["set_menu_picture_id", "locale_id"]),
]
class SetMenuPictureTranslation extends ProductPictureTranslation
{
    // Properties :

    /**
     * @var \App\Entity\SetMenuPicture the picture.
     */
    #[
        ORM\JoinColumn(
            name: "set_menu_picture_id",
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(
            targetEntity: SetMenuPicture::class,
            inversedBy: "translations"
        )
    ]
    private SetMenuPicture $picture;


    // Magic methods :

    /**
     * @param \App\Entity\SetMenuPicture $picture the set menu's picture.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $alternative the alternative.
     * @param string|null $title the title.
     */
    public function __construct(
        SetMenuPicture $picture,
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
