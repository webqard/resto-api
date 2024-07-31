<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\ApiResource\ApiResponse;
use App\ApiResource\CurrencyInput;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Controller\Currency\CurrencyPutController;
use App\Controller\SendErrorController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyGetRepository;
use App\Repository\Currency\CurrencyPutRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Currency\CurrencyPutProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the currency PUT.
 */
#[
    PA\CoversClass(CurrencyPutController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyInput::class),
    PA\UsesClass(CurrencyPutProcessor::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(CurrencyPutRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\UsesClass(Violation::class),
    PA\UsesClass(Violations::class),
    PA\Group('api'),
    PA\Group('api_currencies'),
    PA\Group('api_currencies_put'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyPutTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a currency
     * needs authentication to be put.
     */
    public function testNeedsAuthenticationToBePut(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/currencies/1');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a currency
     * can not be put
     * without ROLE_PUT_CURRENCY.
     */
    public function testCanNotBePutWithoutRolePutCurrency(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('PUT', '/currencies/1');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_PUT_CURRENCY.
     */
    private function addAUserWithRolePutCurrency(): void
    {
        $this->addAUserWithRole('ROLE_PUT_CURRENCY');
    }

    /**
     * Tests that a currency can be updated.
     */
    public function testCanBePut(): void
    {
        $client = static::createClient();

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyToPut = [
            'code' => 'GBP',
            'decimals' => 3
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currencyToPut));

        self::assertResponseStatusCodeSame(204, 'PUT to "/currencies/1" failed.');

        $apiResponse = $client->getResponse()->getContent();

        self::assertEmpty($apiResponse, 'The PUT success must not return a body.');
    }

    /**
     * Tests that a currency can not be updated
     * with the code of an other one.
     */
    public function testCanNotBePutWithTheCodeOfAnOtherOne(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currencyEUR = new Currency('EUR', 2);
        $currencyGBP = new Currency('GBP', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currencyEUR);
        $entityManager->persist($currencyGBP);
        $entityManager->flush();

        $sameCurrency = json_encode([
            'code' => 'GBP',
            'decimals' => 2
        ]);

        $client->request('PUT', '/currencies/1', content: $sameCurrency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'PUT same currency twice did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code already exist.', $jsonResponse[0]->message);
    }


    /**
     * Tests that a currency can not be updated
     * from an non existant identifier.
     */
    public function testCanNotBePutFromAnNonExistantId(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currency));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'PUT "/currencies/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The resource has not been found.', $jsonResponse->message);
    }


    /**
     * Tests that a currency can not be updated
     * from invalid json.
     */
    public function testCanNotBePutWithInvalidJson(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $client->request('PUT', '/currencies/1', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed for invalid json.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Invalid json.', $jsonResponse->message);
    }


    /**
     * Tests that an empty body request
     * can not create a currency.
     */
    public function testCanNotBePutWithAnEmptyBodyRequest(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $client->request('PUT', '/currencies/1', content: '[]');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed with an empty body request.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Properties are missing.', $jsonResponse->message);
    }


    /**
     * Returns invalid type values.
     * @return array invalid type values.
     */
    public static function getInvalidTypeValues(): array
    {
        return [
            'code is null' => ['code', null],
            'code is an integer (5)' => ['code', 5],
            'code is an array ([])' => ['code', []],
            'decimal is null' => ['decimals', null],
            'decimal is a string ("test")' => ['decimals', 'test'],
            'decimal is a float (1.5)' => ['decimals', 1.5],
            'decimal is an array ([])' => ['decimals', []]
        ];
    }

    /**
     * Tests that an invalid type value
     * can not be updated.
     * @param string $property the property name.
     * @param mixed $invalidTypeValue an invalid type value.
     */
    #[
        PA\DataProvider('getInvalidTypeValues'),
        PA\TestDox('Can not be put when the $_dataName')
    ]
    public function testCanNotBePutWithAnInvalidTypeValue(string $property, mixed $invalidTypeValue): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyWithInvalidTypeCode = [
            'code' => 'GBP',
            'decimals' => 3
        ];
        $currencyWithInvalidTypeCode[$property] = $invalidTypeValue;

        $client->request('PUT', '/currencies/1', content: json_encode($currencyWithInvalidTypeCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed for invalid ' . $property . '.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Type error.', $jsonResponse->message);
    }


    /**
     * Tests that a currency can not be updated
     * with a negative decimal.
     */
    public function testCanNotBePutWithANegativeDecimal(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyWithABlankCode = [
            'code' => 'EUR',
            'decimals' => -2
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currencyWithABlankCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('decimals', $jsonResponse[0]->property);
        self::assertSame('The decimals are negative.', $jsonResponse[0]->message);
    }


    /**
     * Tests that a currency can not be updated
     * with a blank code.
     */
    public function testCanNotBePutWithABlankCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyWithABlankCode = [
            'code' => '',
            'decimals' => 2
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currencyWithABlankCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code is blank.', $jsonResponse[0]->message);
    }


    /**
     * Tests that an invalid code
     * can not be updated.
     */
    public function testCanNotBePutWithAnInvalidCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyWithInvalidCode = [
            'code' => 'aaa_AAA_aaa',
            'decimals' => 2
        ];

        $client->request('PUT', '/currencies/1', content: json_encode($currencyWithInvalidCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code is invalid.', $jsonResponse[0]->message);
    }
}
