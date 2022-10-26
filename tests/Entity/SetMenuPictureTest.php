<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SetMenu;
use App\Entity\SetMenuPicture;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuPicture entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuPicture
 * @covers ::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPicture::__construct
 * @group entities
 * @group entities_setMenuPicture
 * @group setMenuPicture
 */
final class SetMenuPictureTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();
        $setMenuPicture = new SetMenuPicture(
            $setMenu,
            'test-source',
        );

        self::assertNull($setMenuPicture->getId());
    }
}
