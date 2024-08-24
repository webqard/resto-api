<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\RoleInput;
use App\ApiResource\RoleTranslationInput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API role input.
 */
#[
    PA\CoversClass(RoleInput::class),
    PA\UsesClass(RoleTranslationInput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_roleInput'),
    PA\Group('role')
]
final class RoleInputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the role
     * can return the name.
     */
    public function testCanGetTheName(): void
    {
        $roleInput = new RoleInput('name');

        self::assertSame('name', $roleInput->getName());
    }


    /**
     * Test that the roleInput
     * can have no translation.
     */
    public function testCanInitialiseWithoutTranslation(): void
    {
        $roleInput = new RoleInput('name');

        self::assertNull($roleInput->getTranslations());
    }


    /**
     * Test that the roleInput
     * can have a translation.
     */
    public function testCanInitialiseWithATranslation(): void
    {
        $roleTranslation = new RoleTranslationInput('description', 1);
        $roleInput = new RoleInput('name', [$roleTranslation]);

        self::assertCount(1, $roleInput->getTranslations());
    }


    /**
     * Test that the roleInput
     * can have many translations.
     */
    public function testCanInitialiseWithManyTranslations(): void
    {
        $roleTranslation = new RoleTranslationInput('description', 1);
        $roleTranslation2 = new RoleTranslationInput('description2', 2);
        $roleInput = new RoleInput(
            'name',
            [
                $roleTranslation,
                $roleTranslation2
            ]
        );

        self::assertCount(2, $roleInput->getTranslations());
    }
}
