<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\ProductTranslation;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductTranslation entity.
 *
 * @coversDefaultClass \App\Entity\ProductTranslation
 * @covers ::__construct
 * @uses \App\Entity\Locale::__construct
 * @group entities
 * @group entities_productTranslation
 * @group productTranslation
 */
final class ProductTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $locale = new Locale('en_GB');
        $productTranslation = $this->getMockForAbstractClass(
            ProductTranslation::class,
            [
                'test-name',
                'test-slug',
                $locale
            ]
        );

        self::assertNull($productTranslation->getId());
    }
}
