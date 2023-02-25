<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CourseTranslation;
use App\Entity\Locale;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CourseTranslation entity.
 */
#[
    PA\CoversClass(CourseTranslation::class),
    PA\UsesClass(Course::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductTranslation::class),
    PA\Group('entities'),
    PA\Group('entities_courseTranslation'),
    PA\Group('courseTranslation')
]
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
