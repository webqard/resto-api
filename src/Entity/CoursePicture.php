<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\CourseAssociation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The course's picture entity.
 */
#[ORM\Entity()]
class CoursePicture extends ProductPicture
{
    // Traits :
    use CourseAssociation;


    // Properties :

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\OneToMany(
            targetEntity: CoursePictureTranslation::class,
            mappedBy: 'picture',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;


    // Magic methods :

    /**
     * @param \App\Entity\Course $course the course.
     * @param string $source the source.
     * @param int $priority the priority.
     */
    public function __construct(
        Course $course,
        string $source,
        int $priority = 0
    ) {
        parent::__construct(
            $source,
            $priority
        );

        $this->course = $course;
        $this->translations = new ArrayCollection();
    }
}
