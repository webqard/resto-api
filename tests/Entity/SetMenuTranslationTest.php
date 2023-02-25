<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\SetMenu;
use App\Entity\SetMenuTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuTranslation entity.
 */
#[
    PA\CoversClass(SetMenuTranslation::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductTranslation::class),
    PA\UsesClass(SetMenu::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuTranslation'),
    PA\Group('setMenuTranslation')
]
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
