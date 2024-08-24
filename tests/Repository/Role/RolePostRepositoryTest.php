<?php

declare(strict_types=1);

namespace App\Tests\Repository\Role;

use App\Entity\Role;
use App\Repository\Role\RolePostRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the role POST repository.
 */
#[
    PA\CoversClass(RolePostRepository::class),
    PA\UsesClass(Role::class),
    PA\Group('repositories'),
    PA\Group('repository_roles'),
    PA\Group('repository_roles_post'),
    PA\Group('role')
]
final class RolePostRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a role can be saved.
     */
    public function testCanPostARole(): void
    {
        $role = new Role('ROLE_POST_ROLE');

        static::createClient();
        $roleRepository = static::getContainer()->get(RolePostRepository::class);
        $roleRepository->save($role);

        self::assertSame(1, $role->getId(), 'The role must have an identifier.');
        self::assertSame('ROLE_POST_ROLE', $role->getName());
    }
}
