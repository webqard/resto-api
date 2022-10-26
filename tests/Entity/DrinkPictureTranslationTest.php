<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkPicture;
use App\Entity\DrinkPictureTranslation;
use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkPictureTranslation entity.
 *
 * @coversDefaultClass \App\Entity\DrinkPictureTranslation
 * @covers ::__construct
 * @uses \App\Entity\Drink::__construct
 * @uses \App\Entity\DrinkPicture::__construct
 * @uses \App\Entity\Locale::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPicture::__construct
 * @uses \App\Entity\ProductPictureTranslation::__construct
 * @group entities
 * @group entities_drinkPictureTranslation
 * @group drinkPictureTranslation
 */
final class DrinkPictureTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();
        $drinkPicture = new DrinkPicture($drink, 'test-source');
        $locale = new Locale('en_GB');
        $drinkPictureTranslation = new DrinkPictureTranslation(
            $drinkPicture,
            $locale
        );

        self::assertNull($drinkPictureTranslation->getId());
    }
}
