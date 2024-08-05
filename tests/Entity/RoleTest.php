<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\Role;
use App\Entity\RoleTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Role entity.
 */
#[
    PA\CoversClass(Role::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(RoleTranslation::class),
    PA\Group('entities'),
    PA\Group('entities_role'),
    PA\Group('role')
]
final class RoleTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $role = new Role('name');

        self::assertNull($role->getId());
    }


    /**
     * Test that the name can be returned and changed.
     */
    public function testCanGetAndSetName(): void
    {
        $role = new Role('name', 2);

        self::assertSame('name', $role->getName());

        $role->setName('new-name');

        self::assertSame('new-name', $role->getName());
    }


    /**
     * Test that the translations
     * are initialised to an empty collection.
     */
    public function testCanInitialiseTheTranslationsToAnEmptyCollection(): void
    {
        $role = new Role('name');

        self::assertCount(0, $role->getTranslations());
    }

    /**
     * Test that a translation
     * can be added and removed.
     */
    public function testCanAddAndRemoveATranslation(): void
    {
        $role = new Role('name');
        $locale = new Locale('en_GB');
        $translation = new RoleTranslation($role, $locale, 'description');

        $role->addTranslation($translation);
        $translations = $role->getTranslations();

        self::assertCount(1, $translations);
        self::assertArrayHasKey(0, $translations);
        self::assertSame('description', $translations[0]->getDescription());

        $role->removeTranslation($translation);

        self::assertCount(0, $translations);
    }
}
