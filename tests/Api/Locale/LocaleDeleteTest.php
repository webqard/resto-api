<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\ApiResource\ApiResponse;
use App\Controller\Locale\LocaleDeleteController;
use App\Entity\Locale;
use App\Entity\User;
use App\Repository\Locale\LocaleDeleteRepository;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the locale DELETE.
 */
#[
    PA\CoversClass(LocaleDeleteController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleDeleteRepository::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_delete'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocaleDeleteTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a locale
     * needs authentication to be deleted.
     */
    public function testNeedsAuthenticationToBeDeleted(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/locales/1');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a locale
     * can not be deleted
     * without ROLE_DELETE_LOCALE.
     */
    public function testCanNotBeDeletedWithoutRoleDeleteLocale(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('DELETE', '/locales/1');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_DELETE_LOCALE.
     */
    private function addAUserWithRoleDeleteLocale(): void
    {
        $this->addAUserWithRole('ROLE_DELETE_LOCALE');
    }

    /**
     * Tests that a locale can be deleted.
     */
    public function testCanBeDeleted(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleDeleteLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('DELETE', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(204, 'DELETE "/locales/1" failed.');
        self::assertEmpty($apiResponse, 'The response must not have a body.');
    }


    /**
     * Tests that a locale can not be deleted
     * from an non existant identifier.
     */
    public function testCanNotBeDeletedFromAnNonExistantId(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleDeleteLocale();
        $this->authenticateClient($client);

        $client->request('DELETE', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'DELETE "/locales/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The resource has not been found.', $jsonResponse->message);
    }
}
