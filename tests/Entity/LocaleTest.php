<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Locale entity.
 *
 * @coversDefaultClass \App\Entity\Locale
 * @covers ::__construct
 * @group entities
 * @group entities_locale
 * @group locale
 */
final class LocaleTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $locale = new Locale('en_GB');

        self::assertNull($locale->getId());
    }


    /**
     * Test that the code can be returned and changed.
     *
     * @covers ::getCode
     * @covers ::setCode
     */
    public function testCanGetAndCode(): void
    {
        $locale = new Locale('en_GB');

        self::assertSame('en_GB', $locale->getCode());

        $locale->setCode('fr_FR');

        self::assertSame('fr_FR', $locale->getCode());
    }
}
