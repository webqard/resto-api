<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkTranslation;
use App\Entity\Locale;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkTranslation entity.
 */
#[
    PA\CoversClass(DrinkTranslation::class),
    PA\UsesClass(Drink::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductTranslation::class),
    PA\Group('entities'),
    PA\Group('entities_drinkTranslation'),
    PA\Group('drinkTranslation')
]
final class DrinkTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();
        $locale = new Locale('en_GB');
        $drinkTranslation = new DrinkTranslation(
            $drink,
            'test-name',
            'test-slug',
            $locale
        );

        self::assertNull($drinkTranslation->getId());
    }
}
