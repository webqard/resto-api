<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\Product;
use App\Entity\ProductPicture;
use App\Entity\ProductPictureTranslation;
use App\Entity\SetMenu;
use App\Entity\SetMenuPicture;
use App\Entity\SetMenuPictureTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuPictureTranslation entity.
 */
#[
    PA\CoversClass(ProductPictureTranslation::class),
    PA\CoversClass(SetMenuPictureTranslation::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductPicture::class),
    PA\UsesClass(SetMenu::class),
    PA\UsesClass(SetMenuPicture::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuPictureTranslation'),
    PA\Group('setMenuPictureTranslation')
]
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
