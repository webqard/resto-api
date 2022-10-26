<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CoursePrice;
use App\Entity\Currency;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CoursePrice entity.
 *
 * @coversDefaultClass \App\Entity\CoursePrice
 * @covers ::__construct
 * @uses \App\Entity\Course::__construct
 * @uses \App\Entity\Currency::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPrice::__construct
 * @group entities
 * @group entities_coursePrice
 * @group coursePrice
 */
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
