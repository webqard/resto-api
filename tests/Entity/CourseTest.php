<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Course entity.
 *
 * @coversDefaultClass \App\Entity\Course
 * @covers ::__construct
 * @uses \App\Entity\Product::__construct
 * @group entities
 * @group entities_course
 * @group course
 */
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
