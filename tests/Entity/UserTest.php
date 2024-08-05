<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Group;
use App\Entity\Locale;
use App\Entity\User;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the User entity.
 */
#[
    PA\CoversClass(User::class),
    PA\UsesClass(Group::class),
    PA\UsesClass(Locale::class),
    PA\Group('entities'),
    PA\Group('entities_user'),
    PA\Group('user')
]
final class UserTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $user = new User('login', 'password');

        self::assertNull($user->getId());
    }


    /**
     * Test that connectedAt
     * is initialised to null by default.
     */
    public function testCanInitialiseConnectedAtToNullByDefault(): void
    {
        $user = new User('login', 'password');

        self::assertNull($user->getConnectedAt());
    }

    /**
     * Test that connectedAt
     * can be initialised to a specific DateTimeImmutable.
     */
    public function testCanInitialiseConnectedAtToASpecificMoment(): void
    {
        $connectedAt = new \DateTimeImmutable();
        $connectedAt->setDate(2024, 01, 01);
        $connectedAt->setTime(0, 0);
        $user = new User('login', 'password', connectedAt: $connectedAt);

        self::assertSame($connectedAt, $user->getConnectedAt());
    }


    /**
     * Test that active
     * is initialised to true by default.
     */
    public function testCanInitialiseActiveToTrueByDefault(): void
    {
        $user = new User('login', 'password');

        self::assertTrue($user->isActive());
    }

    /**
     * Test that active
     * can be initialised to false.
     */
    public function testCanInitialiseActiveToFalse(): void
    {
        $user = new User('login', 'password', active: false);

        self::assertFalse($user->isActive());
    }


    /**
     * Test that the login can be returned and changed.
     */
    public function testCanGetAndSetLogin(): void
    {
        $user = new User('login', 'password');

        self::assertSame('login', $user->getLogin());

        $user->setLogin('new-login');

        self::assertSame('new-login', $user->getLogin());
    }


    /**
     * Test that the password can be returned and changed.
     */
    public function testCanGetAndSetPassword(): void
    {
        $user = new User('login', 'password');

        self::assertSame('password', $user->getPassword());

        $user->setPassword('new-password');

        self::assertSame('new-password', $user->getPassword());
    }


    /**
     * Test that the groups
     * are initialised to an empty collection.
     */
    public function testCanInitialiseTheGroupsToAnEmptyCollection(): void
    {
        $user = new User('login', 'password');

        self::assertCount(0, $user->getGroups());
    }

    /**
     * Test that a group
     * can be added and removed.
     */
    public function testCanAddAndRemoveAGroup(): void
    {
        $user = new User('login', 'password');
        $group = new Group();

        $user->addGroup($group);
        $groups = $user->getGroups();

        self::assertCount(1, $groups);

        $user->removeGroup($group);

        self::assertCount(0, $groups);
    }


    /**
     * Test that the roles
     * can be returned and changed.
     */
    public function testCanGetAndSetRoles(): void
    {
        $user = new User(
            'login',
            'password',
            new \DateTimeImmutable()
        );

        self::assertSame([], $user->getRoles());

        $user->setRoles(['ROLE_1']);
        $roles = $user->getRoles();

        self::assertCount(1, $roles);
        self::assertArrayHasKey(0, $roles);
        self::assertSame('ROLE_1', $roles[0]);
    }


    /**
     * Test that the credentials
     * can be erased.
     */
    public function testCanEraseCredentials(): void
    {
        $user = new User('login', 'password');

        self::assertSame('password', $user->getPassword());

        $user->eraseCredentials();

        self::assertSame('', $user->getPassword());
    }


    /**
     * Test that the UserIdentifier
     * can be returned.
     */
    public function testCanGetUserIdentifier(): void
    {
        $user = new User('login', 'password');

        self::assertSame('login', $user->getUserIdentifier());
    }
}
