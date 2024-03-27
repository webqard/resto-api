<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CoursePicture;
use App\Entity\Product;
use App\Entity\ProductPicture;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CoursePicture entity.
 */
#[
    PA\CoversClass(CoursePicture::class),
    PA\CoversClass(ProductPicture::class),
    PA\UsesClass(Course::class),
    PA\UsesClass(Product::class),
    PA\Group('entities'),
    PA\Group('entities_coursePicture'),
    PA\Group('coursePicture')
]
final class CoursePictureTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $course = new Course();
        $coursePicture = new CoursePicture(
            $course,
            'test-source',
        );

        self::assertNull($coursePicture->getId());
    }
}
