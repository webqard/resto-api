<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleOutput;
use App\Controller\Locale\LocaleGetController;
use App\Entity\Locale;
use App\Entity\User;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Locale\LocaleProvider;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the locale GET.
 */
#[
    PA\CoversClass(LocaleGetController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocaleOutput::class),
    PA\UsesClass(LocaleProvider::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_get'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocaleGetTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a locale
     * needs authentication to be returned.
     */
    public function testNeedsAuthenticationToBeReturned(): void
    {
        $client = static::createClient();

        $client->request('GET', '/locales/1');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a locale
     * can not be returned
     * without ROLE_GET_LOCALE.
     */
    public function testCanNotBeReturnedWithoutRoleGetLocale(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();

        $this->authenticateClient($client);

        $client->request('GET', '/locales/1');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_GET_LOCALE.
     */
    private function addAUserWithRoleGetLocale(): void
    {
        $this->addAUserWithRole('ROLE_GET_LOCALE');
    }

    /**
     * Tests that a locale can be returned.
     */
    public function testCanBeReturned(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('GET', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('en_GB', $jsonResponse->code);
    }


    /**
     * Tests that a locale can not be returned
     * from an non existant identifier.
     */
    public function testCanNotBeReturnedFromAnNonExistantId(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetLocale();
        $this->authenticateClient($client);

        $client->request('GET', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The resource has not been found.', $jsonResponse->message);
    }
}
