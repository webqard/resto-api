<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\SetMenu;
use App\Entity\SetMenuCourse;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuCourse entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuCourse
 * @covers ::__construct
 * @uses \App\Entity\Course::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @uses \App\Entity\SetMenuProduct::__construct
 * @group entities
 * @group entities_setMenuCourse
 * @group setMenuCourse
 */
final class SetMenuCourseTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $course = new Course();
        $setMenu = new SetMenu();
        $setMenuCourse = new SetMenuCourse($course, $setMenu);

        self::assertNull($setMenuCourse->getId());
    }
}
