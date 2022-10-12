<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\CourseAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The course's price entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["course_id", "currency_id"])
]
class CoursePrice extends ProductPrice
{
    // Traits :
    use CourseAssociation;


    // Magic methods :

    /**
     * @param \App\Entity\Course $course the course.
     * @param int $value the value in the smallest unit.
     * @param \App\Entity\Currency $currency the currency.
     * @param \DateTimeImmutable $beginDate the begin date.
     * @param \DateTimeImmutable|null $endDate the end date.
     */
    public function __construct(
        Course $course,
        int $value,
        Currency $currency,
        \DateTimeImmutable $beginDate,
        ?\DateTimeImmutable $endDate = null
    ) {
        parent::__construct(
            $value,
            $currency,
            $beginDate,
            $endDate
        );

        $this->course = $course;
    }
}
