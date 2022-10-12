<?php

declare(strict_types=1);

namespace App\Entity\Association;

use App\Entity\Course;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait that adds a 'Course' field to an entity.
 */
trait CourseAssociation
{
    // Properties :

    /**
     * @var \App\Entity\Course the course.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: Course::class)
    ]
    private Course $course;
}
