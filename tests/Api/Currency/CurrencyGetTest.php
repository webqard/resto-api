<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\ApiResource\ApiResponse;
use App\ApiResource\CurrencyOutput;
use App\Controller\Currency\CurrencyGetController;
use App\Entity\Currency;
use App\Repository\Currency\CurrencyGetRepository;
use App\State\Currency\CurrencyProvider;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency GET.
 */
#[
    PA\CoversClass(CurrencyGetController::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(CurrencyOutput::class),
    PA\UsesClass(CurrencyProvider::class),
    PA\Group('api'),
    PA\Group('api_currencies'),
    PA\Group('api_currencies_get'),
    PA\Group('currency')
]
final class CurrencyGetTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be returned.
     */
    public function testCanGetACurrency(): void
    {
        $client = static::createClient();
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
     * Return invalid type Ids.
     * @return array invalid type Ids.
     */
    public static function getInvalidTypeId(): array
    {
        return [
            'a string (test)' => ['test'],
            'a float (1.5)' => [1.5]
        ];
    }

    /**
     * Tests that a identifier can not be returned
     * from an invalid type identifier.
     * @param mixed $invalidTypeId an invalid type identifier.
     */
    #[
        PA\DataProvider('getInvalidTypeId'),
        PA\TestDox('Can not get a currency from $_dataName')
    ]
    public function testCanNotGetACurrencyFromAnInvalidTypeId(mixed $invalidTypeId): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $client->request('GET', '/currencies/' . $invalidTypeId);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'GET "/currencies/' . $invalidTypeId . '" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }


    /**
     * Tests that a currency can not be returned
     * from an non existant identifier.
     */
    public function testCanNotGetACurrencyFromAnNonExistantId(): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $client->request('GET', '/currencies/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'GET "/currencies/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}
