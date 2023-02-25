<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CoursePicture;
use App\Entity\CoursePictureTranslation;
use App\Entity\Locale;
use App\Entity\Product;
use App\Entity\ProductPicture;
use App\Entity\ProductPictureTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CoursePictureTranslation entity.
 */
#[
    PA\CoversClass(CoursePictureTranslation::class),
    PA\UsesClass(Course::class),
    PA\UsesClass(CoursePicture::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductPicture::class),
    PA\UsesClass(ProductPictureTranslation::class),
    PA\Group('entities'),
    PA\Group('entities_coursePictureTranslation'),
    PA\Group('coursePictureTranslation')
]
final class CoursePictureTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $course = new Course();
        $coursePicture = new CoursePicture($course, 'test-source');
        $locale = new Locale('en_GB');
        $coursePictureTranslation = new CoursePictureTranslation(
            $coursePicture,
            $locale
        );

        self::assertNull($coursePictureTranslation->getId());
    }
}
