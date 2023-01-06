<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\LocaleOutput;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API locale output.
 *
 * @coversDefaultClass \App\ApiResource\LocaleOutput
 * @covers ::__construct
 * @covers ::jsonSerialize
 * @group apiResource
 * @group apiResource_localeOutput
 * @group locale
 */
final class LocaleOutputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the locale can be serialised.
     */
    public function testCanSerialiseLocale(): void
    {
        $localeOutput = new LocaleOutput('en_GB');

        $unserialisedLocale = json_decode(json_encode($localeOutput));

        self::assertObjectHasAttribute('code', $unserialisedLocale);
        self::assertSame('en_GB', $unserialisedLocale->code);
    }
}
