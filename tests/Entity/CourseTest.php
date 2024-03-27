<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\Product;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Course entity.
 */
#[
    PA\CoversClass(Course::class),
    PA\CoversClass(Product::class),
    PA\Group('entities'),
    PA\Group('entities_course'),
    PA\Group('course')
]
final class CourseTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $course = new Course();

        self::assertNull($course->getId());
    }
}
