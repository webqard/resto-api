<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SetMenu;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenu entity.
 *
 * @coversDefaultClass \App\Entity\SetMenu
 * @covers ::__construct
 * @uses \App\Entity\Product::__construct
 * @group entities
 * @group entities_setMenu
 * @group setMenu
 */
final class SetMenuTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();

        self::assertNull($setMenu->getId());
    }
}
