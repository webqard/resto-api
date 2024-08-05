<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\Role;
use App\Entity\RoleTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the RoleTranslation entity.
 */
#[
    PA\CoversClass(RoleTranslation::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Role::class),
    PA\Group('entities'),
    PA\Group('entities_roleTranslation'),
    PA\Group('role')
]
final class RoleTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $role = new Role('name');
        $locale = new Locale('en_GB');
        $roleTranslation = new RoleTranslation($role, $locale, 'description');

        self::assertNull($roleTranslation->getId());
    }


    /**
     * Test that the locale
     * can be returned and changed.
     */
    public function testCanGetAndSetLocale(): void
    {
        $role = new Role('name');
        $locale = new Locale('en_GB');
        $roleTranslation = new RoleTranslation($role, $locale, 'description');

        self::assertSame($locale, $roleTranslation->getLocale());

        $newLocale = new Locale('fr_FR');
        $roleTranslation->setLocale($newLocale);

        self::assertSame($newLocale, $roleTranslation->getLocale());
    }


    /**
     * Test that the description
     * can be returned and changed.
     */
    public function testCanGetAndSetDescription(): void
    {
        $role = new Role('name');
        $locale = new Locale('en_GB');
        $roleTranslation = new RoleTranslation($role, $locale, 'description');

        self::assertSame('description', $roleTranslation->getDescription());

        $roleTranslation->setDescription('new-description');

        self::assertSame('new-description', $roleTranslation->getDescription());
    }
}
