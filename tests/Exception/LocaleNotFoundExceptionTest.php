<?php

declare(strict_types=1);

namespace App\Tests\Exception;

use App\Exception\LocaleNotFoundException;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests LocaleNotFoundException.
 */
#[
    PA\CoversClass(LocaleNotFoundException::class),
    PA\Group('exceptions'),
    PA\Group('exceptions_localeNotFoundException')
]
final class LocaleNotFoundExceptionTest extends TestCase
{
    // Methods :

    /**
     * Test that the default code is 0.
     */
    public function testCanHave0AsDefaultCode(): void
    {
        $exception = new LocaleNotFoundException('message', 1);

        self::assertSame(0, $exception->getCode());
    }


    /**
     * Test that the message can be given.
     */
    public function testCanGetTheMessage(): void
    {
        $exception = new LocaleNotFoundException('message', 1);

        self::assertSame('message', $exception->getMessage());
    }


    /**
     * Test that the given field can be given.
     */
    public function testCanGetTheIndentifier(): void
    {
        $exception = new LocaleNotFoundException('message', 1);

        self::assertSame(1, $exception->getIndentifier());
    }
}
