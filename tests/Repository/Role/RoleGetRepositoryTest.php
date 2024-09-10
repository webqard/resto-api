<?php

declare(strict_types=1);

namespace App\Tests\Repository\Role;

use App\Entity\Locale;
use App\Entity\Role;
use App\Entity\RoleTranslation;
use App\Repository\Role\RoleGetRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the role GET repository.
 */
#[
    PA\CoversClass(RoleGetRepository::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Role::class),
    PA\UsesClass(RoleTranslation::class),
    PA\Group('repositories'),
    PA\Group('repository_roles'),
    PA\Group('repository_roles_get'),
    PA\Group('role')
]
final class RoleGetRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a role can be found.
     */
    public function testCanFindARole(): void
    {
        static::createClient();
        $role = new Role('ROLE_GET_ROLE');

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->flush();

        $roleRepository = static::getContainer()->get(RoleGetRepository::class);
        $foundRole = $roleRepository->find(1);

        self::assertSame(1, $foundRole->getId(), 'The role must have an identifier.');
        self::assertSame('ROLE_GET_ROLE', $foundRole->getName());
    }
}
