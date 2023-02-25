<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\Product;
use App\Entity\SetMenu;
use App\Entity\SetMenuCourse;
use App\Entity\SetMenuProduct;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuCourse entity.
 */
#[
    PA\CoversClass(SetMenuCourse::class),
    PA\UsesClass(Course::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(SetMenu::class),
    PA\UsesClass(SetMenuProduct::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuCourse'),
    PA\Group('setMenuCourse')
]
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
