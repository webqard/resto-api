<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The course picture's translation entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["course_picture_id", "locale_id"])
]
class CoursePictureTranslation extends ProductPictureTranslation
{
    // Properties :

    /**
     * @var \App\Entity\CoursePicture the picture.
     */
    #[
        ORM\JoinColumn(
            name: "course_picture_id",
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(
            targetEntity: CoursePicture::class,
            inversedBy: "translations"
        )
    ]
    private CoursePicture $picture;


    // Magic methods :

    /**
     * @param \App\Entity\CoursePicture $picture the course's picture.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $alternative the alternative.
     * @param string|null $title the title.
     */
    public function __construct(
        CoursePicture $picture,
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
