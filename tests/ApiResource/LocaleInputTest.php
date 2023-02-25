<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\LocaleInput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API locale input.
 */
#[
    PA\CoversClass(LocaleInput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_localeInput'),
    PA\Group('locale')
]
final class LocaleInputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the locale can return code.
     */
    public function testCanGetCode(): void
    {
        $localeInput = new LocaleInput('en_GB');

        self::assertSame('en_GB', $localeInput->getCode());
    }
}
