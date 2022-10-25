<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency GET.
 *
 * @coversDefaultClass \App\Controller\Currency\CurrencyGetController
 * @covers ::__construct
 * @covers ::get
 * @uses \App\Repository\Currency\CurrencyGetRepository::__construct
 * @group api
 * @group api_currencies
 * @group api_currencies_get
 * @group currency
 */
final class CurrencyGetTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be returned.
     *
     * @uses \App\ApiResource\CurrencyOutput::__construct
     * @uses \App\ApiResource\CurrencyOutput::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::getCode
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\State\CurrencyProvider::provideCurrencyOutput
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

        self::assertObjectHasAttribute('code', $jsonResponse);
        self::assertSame('EUR', $jsonResponse->code);
        self::assertObjectHasAttribute('decimals', $jsonResponse);
        self::assertSame(2, $jsonResponse->decimals);
    }


    /**
     * Return invalid type Ids.
     * @return array invalid type Ids.
     */
    private function getInvalidTypeId(): array
    {
        return [
            'id is a string (test)' => ['test'],
            'id is a float (1.5)' => [1.5]
        ];
    }

    /**
     * Tests that a identifier can not be returned
     * from an invalid type identifier.
     * @param mixed $invalidTypeId an invalid type identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @dataProvider getInvalidTypeId
     */
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

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }


    /**
     * Tests that a currency can not be returned
     * from an non existant identifier.
     *
     * @covers \App\ApiResource\ApiResponse::__construct
     * @covers \App\ApiResource\ApiResponse::jsonSerialize
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

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}
