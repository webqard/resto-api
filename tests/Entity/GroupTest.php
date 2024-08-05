<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\Group;
use App\Entity\GroupTranslation;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Group entity.
 */
#[
    PA\CoversClass(Group::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(GroupTranslation::class),
    PA\UsesClass(Role::class),
    PA\UsesClass(User::class),
    PA\Group('entities'),
    PA\Group('entities_group'),
    PA\Group('group')
]
final class GroupTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $group = new Group('name');

        self::assertNull($group->getId());
    }


    /**
     * Test that the translations
     * are initialised to an empty collection.
     */
    public function testCanInitialiseTheTranslationsToAnEmptyCollection(): void
    {
        $group = new Group('name');

        self::assertCount(0, $group->getTranslations());
    }

    /**
     * Test that a translation
     * can be added and removed.
     */
    public function testCanAddAndRemoveATranslation(): void
    {
        $group = new Group('name');
        $locale = new Locale('en_GB');
        $translation = new GroupTranslation($group, $locale, 'name');

        $group->addTranslation($translation);
        $translations = $group->getTranslations();

        self::assertCount(1, $translations);
        self::assertArrayHasKey(0, $translations);
        self::assertSame('name', $translations[0]->getName());

        $group->removeTranslation($translation);

        self::assertCount(0, $translations);
    }


    /**
     * Test that the roles
     * are initialised to an empty collection.
     */
    public function testCanInitialiseTheRolesToAnEmptyCollection(): void
    {
        $group = new Group('name');

        self::assertCount(0, $group->getRoles());
    }

    /**
     * Test that a role
     * can be added and removed.
     */
    public function testCanAddAndRemoveARole(): void
    {
        $group = new Group('name');
        $role = new Role('role-name');

        $group->addRole($role);
        $roles = $group->getRoles();

        self::assertCount(1, $roles);
        self::assertArrayHasKey(0, $roles);
        self::assertSame('role-name', $roles[0]->getName());

        $group->removeRole($role);

        self::assertCount(0, $roles);
    }


    /**
     * Test that the users
     * are initialised to an empty collection.
     */
    public function testCanInitialiseTheUsersToAnEmptyCollection(): void
    {
        $group = new Group('name');

        self::assertCount(0, $group->getUsers());
    }

    /**
     * Test that a user
     * can be added and removed.
     */
    public function testCanAddAndRemoveAUser(): void
    {
        $group = new Group('name');
        $user = new User('login', 'password', new \DateTimeImmutable());

        $group->addUser($user);
        $users = $group->getUsers();

        self::assertCount(1, $users);
        self::assertArrayHasKey(0, $users);
        self::assertSame('login', $users[0]->getLogin());

        $group->removeUser($user);

        self::assertCount(0, $users);
    }
}
