<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CoursePicture;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CoursePicture entity.
 *
 * @coversDefaultClass \App\Entity\CoursePicture
 * @covers ::__construct
 * @uses \App\Entity\Course::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPicture::__construct
 * @group entities
 * @group entities_coursePicture
 * @group coursePicture
 */
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
