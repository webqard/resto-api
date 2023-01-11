<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd;

use App\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database CRUD for the currency.
 *
 * @uses \App\Entity\Currency::__construct
 * @group e2e
 * @group e2e_currency
 * @group currency
 */
final class CurrencyTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be created in the database.
     *
     * @covers \App\Controller\Currency\CurrencyPostController::post
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\ApiResource\CurrencyOutput::__construct
     * @uses \App\ApiResource\CurrencyOutput::jsonSerialize
     * @uses \App\ApiResource\ResourceLink::__construct
     * @uses \App\ApiResource\ResourceLink::jsonSerialize
     * @uses \App\Controller\Currency\CurrencyGetController::__construct
     * @uses \App\Controller\Currency\CurrencyGetController::get
     * @uses \App\Controller\Currency\CurrencyPostController::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Repository\Currency\CurrencyGetRepository::__construct
     * @uses \App\Repository\Currency\CurrencyPostRepository::__construct
     * @uses \App\Repository\Currency\CurrencyPostRepository::save
     * @uses \App\State\Currency\CurrencyPostProcessor::getEntity
     * @uses \App\State\Currency\CurrencyProvider::provideCurrencyOutput
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $noCurrency = $entityManager->find(Currency::class, 1);

        self::assertNull($noCurrency);

        $currencyToPost = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->request('POST', '/currencies', content: json_encode($currencyToPost));
        $postResponse = $client->getResponse()->getContent();
        $jsonPostResponse = json_decode($postResponse, false);

        $client->request('GET', $jsonPostResponse->link);
        $getResponse = $client->getResponse()->getContent();
        $jsonGetResponse = json_decode($getResponse, false);

        $currencyLink = explode('/', $jsonPostResponse->link);
        $currencyLinkLastKey = array_key_last($currencyLink);
        $currencyLinkId = (int)$currencyLink[$currencyLinkLastKey];

        $postedCurrency = $entityManager->find(Currency::class, 1);
        $entityManager->refresh($postedCurrency);

        self::assertSame(1, $currencyLinkId);
        self::assertSame(1, $postedCurrency->getId());
        self::assertSame('EUR', $jsonGetResponse->code);
        self::assertSame('EUR', $postedCurrency->getCode());
        self::assertSame(2, $jsonGetResponse->decimals);
        self::assertSame(2, $postedCurrency->getDecimals());
    }


    /**
     * Tests that a currency can be updated in the database.
     *
     * @covers \App\Controller\Currency\CurrencyPutController::put
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\ApiResource\CurrencyOutput::__construct
     * @uses \App\ApiResource\CurrencyOutput::jsonSerialize
     * @uses \App\Controller\Currency\CurrencyGetController::__construct
     * @uses \App\Controller\Currency\CurrencyGetController::get
     * @uses \App\Controller\Currency\CurrencyPutController::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Currency::setDecimals
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\Repository\Currency\CurrencyGetRepository::__construct
     * @uses \App\Repository\Currency\CurrencyPutRepository::__construct
     * @uses \App\Repository\Currency\CurrencyPutRepository::save
     * @uses \App\State\Currency\CurrencyProvider::provideCurrencyOutput
     * @uses \App\State\Currency\CurrencyPutProcessor::getEntity
     */
    public function testIsUpdatedInTheDatabaseWithPut(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $currency = new Currency('EUR', 2);
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        self::assertSame(1, $currency->getId());

        $client->request('GET', '/currencies/1');
        $getResponse = $client->getResponse()->getContent();
        $jsonGetResponse = json_decode($getResponse, false);

        self::assertSame('EUR', $jsonGetResponse->code);
        self::assertSame(2, $jsonGetResponse->decimals);


        $currencyToPut = [
            'code' => 'GBP',
            'decimals' => 3
        ];
        $client->request('PUT', '/currencies/1', content: json_encode($currencyToPut));


        $client->request('GET', '/currencies/1');
        $getResponseAfterPut = $client->getResponse()->getContent();
        $jsonGetResponseAfterPut = json_decode($getResponseAfterPut, false);

        self::assertSame('GBP', $jsonGetResponseAfterPut->code);
        self::assertSame(3, $jsonGetResponseAfterPut->decimals);

        $currencyAfterPut = $entityManager->find(Currency::class, 1);
        $entityManager->refresh($currencyAfterPut);
        self::assertSame(1, $currencyAfterPut->getId());
        self::assertSame('GBP', $currencyAfterPut->getCode());
        self::assertSame(3, $currencyAfterPut->getDecimals());
    }


    /**
     * Tests that a currency can be deleted in the database.
     *
     * @covers \App\Controller\Currency\CurrencyDeleteController::delete
     * @uses \App\Controller\Currency\CurrencyDeleteController::__construct
     * @uses \App\Repository\Currency\CurrencyDeleteRepository::__construct
     * @uses \App\Repository\Currency\CurrencyDeleteRepository::delete
     */
    public function testIsDeletedInTheDatabaseWithDelete(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $currency = new Currency('EUR', 2);
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        self::assertSame(1, $currency->getId());

        $client->request('DELETE', '/currencies/1');

        $currencyAfterDelete = $entityManager->find(Currency::class, 1);

        self::assertNull($currencyAfterDelete, 'The Currency has not been deleted.');
    }
}
