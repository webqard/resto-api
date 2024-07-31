<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\ApiResource\ApiResponse;
use App\Controller\Currency\CurrencyDeleteController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyDeleteRepository;
use App\Repository\Currency\CurrencyGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the currency DELETE.
 */
#[
    PA\CoversClass(CurrencyDeleteController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyDeleteRepository::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('api'),
    PA\Group('api_currencies'),
    PA\Group('api_currencies_delete'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyDeleteTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a currency
     * needs authentication to be deleted.
     */
    public function testNeedsAuthenticationToBeDeleted(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/currencies/1');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a currency
     * can not be deleted
     * without ROLE_DELETE_CURRENCY.
     */
    public function testCanNotBeDeletedWithoutRoleDeleteCurrency(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('DELETE', '/currencies/1');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_DELETE_CURRENCY.
     */
    private function addAUserWithRoleDeleteCurrency(): void
    {
        $this->addAUserWithRole('ROLE_DELETE_CURRENCY');
    }

    /**
     * Tests that a currency can be deleted.
     */
    public function testCanBeDeleted(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleDeleteCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $client->request('DELETE', '/currencies/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(204, 'DELETE "/currencies/1" failed.');
        self::assertEmpty($apiResponse, 'The response must not have a body.');
    }


    /**
     * Tests that a currency can not be deleted
     * from an non existant identifier.
     */
    public function testCanNotBeDeletedFromAnNonExistantId(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleDeleteCurrency();
        $this->authenticateClient($client);

        $client->request('DELETE', '/currencies/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'DELETE "/currencies/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The resource has not been found.', $jsonResponse->message);
    }
}
