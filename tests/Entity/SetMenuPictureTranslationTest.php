<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SetMenu;
use App\Entity\SetMenuPicture;
use App\Entity\SetMenuPictureTranslation;
use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuPictureTranslation entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuPictureTranslation
 * @covers ::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @uses \App\Entity\SetMenuPicture::__construct
 * @uses \App\Entity\Locale::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPicture::__construct
 * @uses \App\Entity\ProductPictureTranslation::__construct
 * @group entities
 * @group entities_setMenuPictureTranslation
 * @group setMenuPictureTranslation
 */
final class SetMenuPictureTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();
        $setMenuPicture = new SetMenuPicture($setMenu, 'test-source');
        $locale = new Locale('en_GB');
        $setMenuPictureTranslation = new SetMenuPictureTranslation(
            $setMenuPicture,
            $locale
        );

        self::assertNull($setMenuPictureTranslation->getId());
    }
}
