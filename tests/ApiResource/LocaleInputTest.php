<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\LocaleInput;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API locale input.
 *
 * @coversDefaultClass \App\ApiResource\LocaleInput
 * @covers ::__construct
 * @group apiResource
 * @group apiResource_localeInput
 * @group locale
 */
final class LocaleInputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the locale can return code.
     *
     * @covers ::getCode
     */
    public function testCanGetCode(): void
    {
        $localeInput = new LocaleInput('en_GB');

        self::assertSame('en_GB', $localeInput->getCode());
    }
}
