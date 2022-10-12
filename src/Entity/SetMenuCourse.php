<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\CourseAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu's course entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["course_id", "set_menu_id"])
]
class SetMenuCourse extends SetMenuProduct
{
    // Traits :
    use CourseAssociation;


    // Magic methods :

    /**
     * @param \App\Entity\Course $course the course.
     * @param \App\Entity\SetMenu $setMenu the set menu.
     * @param \App\Entity\SetMenuCategory|null $setMenuCategory the set menu category.
     * @param int $priority the priority.
     */
    public function __construct(
        Course $course,
        SetMenu $setMenu,
        ?SetMenuCategory $setMenuCategory = null,
        int $priority = 0
    ) {
        parent::__construct(
            $setMenu,
            $setMenuCategory,
            $priority
        );

        $this->course = $course;
    }
}
