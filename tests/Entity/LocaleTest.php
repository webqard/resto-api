<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Locale entity.
 */
#[
    PA\CoversClass(Locale::class),
    PA\Group('entities'),
    PA\Group('entities_locale'),
    PA\Group('locale')
]
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
     */
    public function testCanGetAndSetCode(): void
    {
        $locale = new Locale('en_GB');

        self::assertSame('en_GB', $locale->getCode());

        $locale->setCode('fr_FR');

        self::assertSame('fr_FR', $locale->getCode());
    }
}
