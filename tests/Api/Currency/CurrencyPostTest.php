<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\ApiResource\ApiResponse;
use App\ApiResource\CurrencyInput;
use App\ApiResource\ResourceLink;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Controller\Currency\CurrencyPostController;
use App\Controller\SendErrorController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyPostRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Currency\CurrencyPostProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the currency POST.
 */
#[
    PA\CoversClass(CurrencyPostController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyInput::class),
    PA\UsesClass(CurrencyPostProcessor::class),
    PA\UsesClass(CurrencyPostRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\UsesClass(Violation::class),
    PA\UsesClass(Violations::class),
    PA\Group('api'),
    PA\Group('api_currencies'),
    PA\Group('api_currencies_post'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyPostTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a currency
     * needs authentication to be posted.
     */
    public function testNeedsAuthenticationToBePosted(): void
    {
        $client = static::createClient();

        $client->request('POST', '/currencies');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a currency
     * can not be posted
     * without ROLE_POST_CURRENCY.
     */
    public function testCanNotBePostedWithoutRolePostCurrency(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('POST', '/currencies');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_POST_CURRENCY.
     */
    private function addAUserWithRolePostCurrency(): void
    {
        $this->addAUserWithRole('ROLE_POST_CURRENCY');
    }

    /**
     * Tests that a currency can be created.
     */
    public function testCanBePosted(): void
    {
        $client = static::createClient();

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $currency = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->jsonRequest('POST', '/currencies', $currency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(201, 'POST to "/currencies" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('/currencies/1', $jsonResponse->link);
    }

    /**
     * Tests that a code of an already existing currency
     * can not be created.
     */
    public function testCanNotBePostedWithACodeThatAlreadyExist(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $sameCurrency = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->jsonRequest('POST', '/currencies', $sameCurrency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'POST same currency twice did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code already exist.', $jsonResponse[0]->message);
    }


    /**
     * Tests that a currency can not be created
     * from invalid json.
     */
    public function testCanNotBePostedWithInvalidJson(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $client->request('POST', '/currencies', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid json.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Invalid json.', $jsonResponse->message);
    }


    /**
     * Tests that an empty body request
     * can not create a currency.
     */
    public function testCanNotBePostedWithAnEmptyBodyRequest(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $client->jsonRequest('POST', '/currencies');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed with an empty body request.');
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
     * can not be created.
     * @param string $property the property name.
     * @param mixed $invalidTypeValue an invalid type value.
     */
    #[
        PA\DataProvider('getInvalidTypeValues'),
        PA\TestDox('Can not be posted when the $_dataName')
    ]
    public function testCanNotBePostedWithAnInvalidTypeValue(string $property, mixed $invalidTypeValue): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $currency = [
            'code' => 'EUR',
            'decimals' => 2
        ];
        $currency[$property] = $invalidTypeValue;
        $client->jsonRequest('POST', '/currencies', $currency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid ' . $property . '.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Type error.', $jsonResponse->message);
    }


    /**
     * Tests that a negative decimal
     * can not be created.
     */
    public function testCanNotBePostedWithANegativeDecimal(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $currency = [
            'code' => 'EUR',
            'decimals' => -2
        ];
        $client->jsonRequest('POST', '/currencies', $currency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('decimals', $jsonResponse[0]->property);
        self::assertSame('The decimals are negative.', $jsonResponse[0]->message);
    }


    /**
     * Tests that a currency can not be created
     * with a blank code.
     */
    public function testCanNotBePostedWithABlankCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $currency = [
            'code' => '',
            'decimals' => 2
        ];

        $client->jsonRequest('POST', '/currencies', $currency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code is blank.', $jsonResponse[0]->message);
    }


    /**
     * Tests that an invalid code
     * can not be created.
     */
    public function testCanNotBePostedWithAnInvalidCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

        $currency = [
            'code' => 'aaa_AAA_aaa',
            'decimals' => 2
        ];
        $client->jsonRequest('POST', '/currencies', $currency);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code is invalid.', $jsonResponse[0]->message);
    }
}
