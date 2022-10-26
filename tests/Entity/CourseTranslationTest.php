<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CourseTranslation;
use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CourseTranslation entity.
 *
 * @coversDefaultClass \App\Entity\CourseTranslation
 * @covers ::__construct
 * @uses \App\Entity\Course::__construct
 * @uses \App\Entity\Locale::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductTranslation::__construct
 * @group entities
 * @group entities_courseTranslation
 * @group courseTranslation
 */
final class CourseTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $course = new Course();
        $locale = new Locale('en_GB');
        $courseTranslation = new CourseTranslation(
            $course,
            'test-name',
            'test-slug',
            $locale
        );

        self::assertNull($courseTranslation->getId());
    }
}
