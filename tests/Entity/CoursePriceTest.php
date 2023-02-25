<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CoursePrice;
use App\Entity\Currency;
use App\Entity\Product;
use App\Entity\ProductPrice;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CoursePrice entity.
 */
#[
    PA\CoversClass(CoursePrice::class),
    PA\UsesClass(Course::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductPrice::class),
    PA\Group('entities'),
    PA\Group('entities_coursePrice'),
    PA\Group('coursePrice')
]
final class CoursePriceTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $course = new Course();
        $currency = new Currency('EUR', 2);
        $coursePrice = new CoursePrice(
            $course,
            5,
            $currency,
            new \DateTimeImmutable()
        );

        self::assertNull($coursePrice->getId());
    }
}
