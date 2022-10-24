<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency DELETE.
 *
 * @coversDefaultClass \App\Controller\Currency\CurrencyDeleteController
 * @covers ::__construct
 * @covers ::delete
 * @uses \App\Repository\Currency\CurrencyDeleteRepository::__construct
 * @group api
 * @group api_currencies
 * @group api_currencies_delete
 * @group currency
 */
final class CurrencyDeleteTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be deleted.
     *
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Repository\Currency\CurrencyDeleteRepository::delete
     */
    public function testCanDeleteACurrency(): void
    {
        $client = static::createClient();
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $client->request('DELETE', '/currencies/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(204, 'DELETE "/currencies/1" failed.');
        self::assertEmpty($apiResponse, 'The response must not have a body.');

        $deletedCurrency = $entityManager->find(Currency::class, 1);

        self::assertNull($deletedCurrency, 'The Currency has not been deleted.');
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
     * Tests that a currency can not be deleted
     * from an invalid type identifier.
     * @param mixed $invalidTypeId an invalid type identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @dataProvider getInvalidTypeId
     */
    public function testCanNotDeleteACurrencyFromAnInvalidTypeId(mixed $invalidTypeId): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $client->request('DELETE', '/currencies/' . $invalidTypeId);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'DELETE "/currencies/' . $invalidTypeId . '" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }


    /**
     * Tests that a currency can not be deleted
     * from an non existant identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     */
    public function testCanNotDeleteACurrencyFromAnNonExistantId(): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $client->request('DELETE', '/currencies/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'DELETE "/currencies/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}
