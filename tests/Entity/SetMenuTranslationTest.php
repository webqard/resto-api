<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SetMenu;
use App\Entity\SetMenuTranslation;
use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuTranslation entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuTranslation
 * @covers ::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @uses \App\Entity\Locale::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductTranslation::__construct
 * @group entities
 * @group entities_setMenuTranslation
 * @group setMenuTranslation
 */
final class SetMenuTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();
        $locale = new Locale('en_GB');
        $setMenuTranslation = new SetMenuTranslation(
            $setMenu,
            'test-name',
            'test-slug',
            $locale
        );

        self::assertNull($setMenuTranslation->getId());
    }
}
