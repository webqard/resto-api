<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkTranslation;
use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkTranslation entity.
 *
 * @coversDefaultClass \App\Entity\DrinkTranslation
 * @covers ::__construct
 * @uses \App\Entity\Drink::__construct
 * @uses \App\Entity\Locale::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductTranslation::__construct
 * @group entities
 * @group entities_drinkTranslation
 * @group drinkTranslation
 */
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
