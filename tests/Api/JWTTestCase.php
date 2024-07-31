<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * A base class for functional tests with a JWT.
 */
abstract class JWTTestCase extends WebTestCase
{
    // Constants :

    /**
     * The login.
     */
    protected const LOGIN = 'login';

    /**
     * The password.
     */
    protected const PASSWORD = 'password';


    // Methods :

    /**
     * Adds a user without role.
     */
    protected function addAUserWithoutRole(): void
    {
        $this->addAUserWithRole('');
    }

    /**
     * Adds a user with a role.
     * @param string $role the role.
     * @param string $login the login.
     * @param string $password the password.
     */
    protected function addAUserWithRole(
        string $role,
        string $login = self::LOGIN,
        string $password = self::PASSWORD
    ): void {
        $container = self::getContainer();
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = new User(
            $login,
            $password,
            roles: [$role]
        );
        $user->setPassword(
            $passwordHasher->hashPassword($user, $password)
        );

        $manager = $container->get('doctrine')->getManager();

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * Returns an authenticated client.
     * @param \Symfony\Bundle\FrameworkBundle\KernelBrowser $client the client.
     * @param string $login the login.
     * @param string $password the password.
     */
    protected function authenticateClient(
        KernelBrowser $client,
        string $login = self::LOGIN,
        string $password = self::PASSWORD
    ): void {
        $client->jsonRequest(
            'POST',
            '/authentication',
            [
                'login' => $login,
                'password' => $password
            ]
        );

        $apiResponse = json_decode($client->getResponse()->getContent(), false);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $apiResponse->token));
    }
}
