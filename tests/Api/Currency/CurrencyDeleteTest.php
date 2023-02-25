<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\ApiResource\ApiResponse;
use App\Controller\Currency\CurrencyDeleteController;
use App\Entity\Currency;
use App\Repository\Currency\CurrencyDeleteRepository;
use App\Repository\Currency\CurrencyGetRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency DELETE.
 */
#[
    PA\CoversClass(CurrencyDeleteController::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyDeleteRepository::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\Group('api'),
    PA\Group('api_currencies'),
    PA\Group('api_currencies_delete'),
    PA\Group('currency')
]
final class CurrencyDeleteTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be deleted.
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
    }


    /**
     * Tests that a currency can not be deleted
     * from an non existant identifier.
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

        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}
