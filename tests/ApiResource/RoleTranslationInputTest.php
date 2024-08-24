<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\RoleTranslationInput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API roleTranslation input.
 */
#[
    PA\CoversClass(RoleTranslationInput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_roleTranslationInput'),
    PA\Group('role')
]
final class RoleTranslationInputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the roleTranslation
     * can return the description.
     */
    public function testCanGetDescription(): void
    {
        $roleTranslationInput = new RoleTranslationInput('description', 1);

        self::assertSame('description', $roleTranslationInput->getDescription());
    }

    /**
     * Tests that the roleTranslation
     * can return the localeId.
     */
    public function testCanGetLocaleId(): void
    {
        $roleTranslationInput = new RoleTranslationInput('description', 1);

        self::assertSame(1, $roleTranslationInput->getLocaleId());
    }
}
