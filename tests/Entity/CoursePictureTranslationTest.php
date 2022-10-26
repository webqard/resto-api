<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CoursePicture;
use App\Entity\CoursePictureTranslation;
use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CoursePictureTranslation entity.
 *
 * @coversDefaultClass \App\Entity\CoursePictureTranslation
 * @covers ::__construct
 * @uses \App\Entity\Course::__construct
 * @uses \App\Entity\CoursePicture::__construct
 * @uses \App\Entity\Locale::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPicture::__construct
 * @uses \App\Entity\ProductPictureTranslation::__construct
 * @group entities
 * @group entities_coursePictureTranslation
 * @group coursePictureTranslation
 */
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
