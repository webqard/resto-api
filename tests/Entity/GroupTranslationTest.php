<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\Group;
use App\Entity\GroupTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the GroupTranslation entity.
 */
#[
    PA\CoversClass(GroupTranslation::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Group::class),
    PA\Group('entities'),
    PA\Group('entities_groupTranslation'),
    PA\Group('group')
]
final class GroupTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $group = new Group('name');
        $locale = new Locale('en_GB');
        $groupTranslation = new GroupTranslation($group, $locale, 'name');

        self::assertNull($groupTranslation->getId());
    }


    /**
     * Test that the description
     * is initialised to null by default.
     */
    public function testCanInitialiseDescriptionToNullByDefault(): void
    {
        $group = new Group('name');
        $locale = new Locale('en_GB');
        $roleTranslation = new GroupTranslation(
            $group,
            $locale,
            'name'
        );

        self::assertNull($roleTranslation->getDescription());
    }

    /**
     * Test that the description
     * can be initialised to a string.
     */
    public function testCanInitialiseDescriptionToAString(): void
    {
        $group = new Group('name');
        $locale = new Locale('en_GB');
        $roleTranslation = new GroupTranslation(
            $group,
            $locale,
            'name',
            'description'
        );

        self::assertSame('description', $roleTranslation->getDescription());
    }


    /**
     * Test that the locale
     * can be returned and changed.
     */
    public function testCanGetAndSetLocale(): void
    {
        $group = new Group('name');
        $locale = new Locale('en_GB');
        $groupTranslation = new GroupTranslation($group, $locale, 'name');

        self::assertSame($locale, $groupTranslation->getLocale());

        $newLocale = new Locale('fr_FR');
        $groupTranslation->setLocale($newLocale);

        self::assertSame($newLocale, $groupTranslation->getLocale());
    }


    /**
     * Test that the name
     * can be returned and changed.
     */
    public function testCanGetAndSetName(): void
    {
        $group = new Group('name');
        $locale = new Locale('en_GB');
        $groupTranslation = new GroupTranslation($group, $locale, 'name');

        self::assertSame('name', $groupTranslation->getName());

        $groupTranslation->setName('new-name');

        self::assertSame('new-name', $groupTranslation->getName());
    }


    /**
     * Test that the description
     * can be returned and changed.
     */
    public function testCanGetAndSetDescription(): void
    {
        $group = new Group('name');
        $locale = new Locale('en_GB');
        $roleTranslation = new GroupTranslation(
            $group,
            $locale,
            'name',
            'description'
        );

        self::assertSame('description', $roleTranslation->getDescription());

        $roleTranslation->setDescription('new-description');

        self::assertSame('new-description', $roleTranslation->getDescription());
    }
}
