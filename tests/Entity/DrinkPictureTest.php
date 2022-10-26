<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkPicture;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkPicture entity.
 *
 * @coversDefaultClass \App\Entity\DrinkPicture
 * @covers ::__construct
 * @uses \App\Entity\Drink::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPicture::__construct
 * @group entities
 * @group entities_drinkPicture
 * @group drinkPicture
 */
final class DrinkPictureTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();
        $drinkPicture = new DrinkPicture(
            $drink,
            'test-source',
        );

        self::assertNull($drinkPicture->getId());
    }
}
