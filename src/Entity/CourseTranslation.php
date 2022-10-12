<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\CourseAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The course's translation entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["course_id", "locale_id"])
]
class CourseTranslation extends ProductTranslation
{
    // Traits :
    use CourseAssociation;


    // Magic methods :

    /**
     * @param \App\Entity\Course $course the course.
     * @param string $name the name.
     * @param string $slug the slug.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $description the description.
     */
    public function __construct(
        Course $course,
        string $name,
        string $slug,
        Locale $locale,
        ?string $description = null
    ) {
        parent::__construct(
            $name,
            $slug,
            $locale,
            $description
        );

        $this->course = $course;
    }
}
