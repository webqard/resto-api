<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Drink entity.
 *
 * @coversDefaultClass \App\Entity\Drink
 * @covers ::__construct
 * @uses \App\Entity\Product::__construct
 * @group entities
 * @group entities_drink
 * @group drink
 */
final class DrinkTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();

        self::assertNull($drink->getId());
    }
}
