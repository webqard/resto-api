<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\User;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AuthenticationFailureHandler;
use App\Security\UserChecker;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the authentication.
 */
#[
    PA\CoversClass(UserChecker::class),
    PA\UsesClass(AuthenticationFailureHandler::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\Group('api'),
    PA\Group('api_authentication')
]
final class AuthenticationTest extends WebTestCase
{
    // Methods :

    /**
     * Adds an active user.
     */
    private function addActiveUser(): void
    {
        $container = self::getContainer();
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = new User(
            'login',
            'password',
            roles: ['ROLE_USER']
        );
        $user->setPassword(
            $passwordHasher->hashPassword($user, 'password')
        );

        $manager = $container->get('doctrine')->getManager();

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * Tests that an active user can authenticate.
     */
    public function testCanAuthenticateAnActiveUser(): void
    {
        $client = static::createClient();

        $this->addActiveUser();

        $client->jsonRequest(
            'POST',
            '/authentication',
            [
                'login' => 'login',
                'password' => 'password'
            ]
        );
        $apiResponse = $client->getResponse()->getContent();
        $token = json_decode($apiResponse, false);

        self::assertResponseStatusCodeSame(200, 'POST to "/authentication" failed.');
        self::assertNotEmpty($token->token, 'Authentication gave no token. May be, JWT keys are missing for the test environment.');
    }


    /**
     * Returns invalid credentials.
     * @return mixed[] invalid credentials.
     */
    public static function getInvalidCredentials(): array
    {
        return [
            'login' => ['login' => 'not-a-login', 'password' => 'password'],
            'password' => ['login' => 'login', 'password' => 'not-a-password']
        ];
    }

    /**
     * Tests that an active user can not authenticate.
     * with invalid credentials.
     * @param string $login the login.
     * @param string $password the password.
     */
    #[
        PA\DataProvider('getInvalidCredentials'),
        PA\TestDox('Can not authenticate with an invalid $_dataName')
    ]
    public function testCanNotAuthenticateWithInvalidCredentials(
        string $login,
        string $password
    ): void {
        $client = static::createClient();

        $this->addActiveUser();

        $client->jsonRequest(
            'POST',
            '/authentication',
            [
                'login' => $login,
                'password' => $password
            ]
        );

        self::assertResponseStatusCodeSame(401);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds an inactive user.
     */
    private function addInactiveUser(): void
    {
        $container = self::getContainer();
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = new User(
            'login',
            'password',
            roles: ['ROLE_USER'],
            active: false
        );
        $user->setPassword(
            $passwordHasher->hashPassword($user, 'password')
        );

        $manager = $container->get('doctrine')->getManager();

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * Tests that an inactive user can not authenticate.
     */
    public function testCanNotAuthenticateAnInactiveUser(): void
    {
        $client = static::createClient();

        $this->addInactiveUser();

        $client->jsonRequest(
            'POST',
            '/authentication',
            [
                'login' => 'login',
                'password' => 'password'
            ]
        );

        self::assertResponseStatusCodeSame(401);
        self::assertSame('', $client->getResponse()->getContent());
    }
}
