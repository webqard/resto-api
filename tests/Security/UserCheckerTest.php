<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tests the UserChecker.
 */
#[
    PA\CoversClass(UserChecker::class),
    PA\UsesClass(User::class),
    PA\Group('security'),
    PA\Group('security_userChecker'),
]
final class UserCheckerTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the UserChecker
     * throws a CustomUserMessageAccountStatusException
     * if the user is not active.
     */
    public function testThrowsACustomUserMessageAccountStatusExceptionIfTheUserIsNotActive(): void
    {
        $this->expectException(CustomUserMessageAccountStatusException::class);

        $user = new User(
            'login',
            'password',
            new \DateTimeImmutable(),
            active: false
        );
        $userChecker = new UserChecker();

        $userChecker->checkPreAuth($user);
    }


    /**
     * Test that the UserChecker
     * does not throw a CustomUserMessageAccountStatusException
     * if the user is not an \App\Entity\User.
     */
    public function testDoesNotThrowACustomUserMessageAccountStatusExceptionIfTheUserIsNotAnAppEntityUser(): void
    {
        $this->expectNotToPerformAssertions();

        $user = $this->createStub(UserInterface::class);
        $userChecker = new UserChecker();

        $userChecker->checkPreAuth($user);
    }
}
