<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\ApiResource\ApiResponse;
use App\ApiResource\CurrencyOutput;
use App\Controller\Currency\CurrencyGetController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\State\Currency\CurrencyProvider;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the currency GET.
 */
#[
    PA\CoversClass(CurrencyGetController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(CurrencyOutput::class),
    PA\UsesClass(CurrencyProvider::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('api'),
    PA\Group('api_currencies'),
    PA\Group('api_currencies_get'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyGetTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a currency
     * needs authentication to be returned.
     */
    public function testNeedsAuthenticationToBeReturned(): void
    {
        $client = static::createClient();

        $client->request('GET', '/currencies/1');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a currency
     * can not be returned
     * without ROLE_GET_CURRENCY.
     */
    public function testCanNotBeReturnedWithoutRoleGetCurrency(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();

        $this->authenticateClient($client);

        $client->request('GET', '/currencies/1');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_GET_CURRENCY.
     */
    private function addAUserWithRoleGetCurrency(): void
    {
        $this->addAUserWithRole('ROLE_GET_CURRENCY');
    }

    /**
     * Tests that a currency can be returned.
     */
    public function testCanBeReturned(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $client->request('GET', '/currencies/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/currencies/1" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('EUR', $jsonResponse->code);
        self::assertSame(2, $jsonResponse->decimals);
    }


    /**
     * Tests that a currency can not be returned
     * from an non existant identifier.
     */
    public function testCanNotBeReturnedFromAnNonExistantId(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetCurrency();
        $this->authenticateClient($client);

        $client->request('GET', '/currencies/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'GET "/currencies/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The resource has not been found.', $jsonResponse->message);
    }
}
