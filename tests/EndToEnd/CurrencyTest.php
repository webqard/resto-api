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
     * @uses \App\ApiResource\ResourceLink::__construct
     * @uses \App\ApiResource\ResourceLink::jsonSerialize
     * @uses \App\Controller\Currency\CurrencyPostController::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Repository\Currency\CurrencySaveRepository::__construct
     * @uses \App\Repository\Currency\CurrencySaveRepository::save
     * @uses \App\State\Currency\CurrencyPostProcessor::getEntity
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $currencyToPost = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->request('POST', '/currencies', content: json_encode($currencyToPost));
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        $currencyLink = explode('/', $jsonResponse->link);
        $currencyLinkLastKey = array_key_last($currencyLink);
        $currencyLinkId = (int)$currencyLink[$currencyLinkLastKey];

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $postedCurrency = $entityManager->find(Currency::class, $currencyLinkId);

        self::assertSame($currencyLinkId, $postedCurrency->getId());
        self::assertSame('EUR', $postedCurrency->getCode());
        self::assertSame(2, $postedCurrency->getDecimals());
    }


    /**
     * Tests that a currency can be read from the database.
     *
     * @covers \App\Controller\Currency\CurrencyGetController::get
     * @uses \App\ApiResource\CurrencyOutput::__construct
     * @uses \App\ApiResource\CurrencyOutput::jsonSerialize
     * @uses \App\Controller\Currency\CurrencyGetController::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Repository\Currency\CurrencyGetRepository::__construct
     * @uses \App\State\Currency\CurrencyProvider::provideCurrencyOutput
     */
    public function testIsReadFromTheDatabaseWithGet(): void
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

        $client->request('GET', '/currencies/' . $currency->getId());
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        self::assertSame('EUR', $jsonResponse->code);
        self::assertSame(2, $jsonResponse->decimals);
    }


    /**
     * Tests that a currency can be updated in the database.
     *
     * @covers \App\Controller\Currency\CurrencyPutController::put
     * @uses \App\ApiResource\CurrencyInput::__construct
     * @uses \App\ApiResource\CurrencyInput::getCode
     * @uses \App\ApiResource\CurrencyInput::getDecimals
     * @uses \App\Controller\Currency\CurrencyPutController::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Currency::setDecimals
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\Repository\Currency\CurrencySaveRepository::__construct
     * @uses \App\Repository\Currency\CurrencySaveRepository::save
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

        $currencyToPut = [
            'code' => 'GBP',
            'decimals' => 3
        ];
        $client->request('PUT', '/currencies/' . $currency->getId(), content: json_encode($currencyToPut));

        $entityManager->refresh($currency);

        self::assertSame('GBP', $currency->getCode());
        self::assertSame(3, $currency->getDecimals());
    }


    /**
     * Tests that a currency can be deleted from the database.
     *
     * @covers \App\Controller\DeleteController::__construct
     * @covers \App\Controller\DeleteController::delete
     * @covers \App\Controller\Currency\CurrencyDeleteController::delete
     * @uses \App\Controller\Currency\CurrencyDeleteController::__construct
     * @uses \App\Repository\Currency\CurrencyDeleteRepository::__construct
     * @uses \App\Repository\Currency\CurrencyDeleteRepository::delete
     */
    public function testIsDeletedFromTheDatabaseWithDelete(): void
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
        $currencyId = $currency->getId();

        $client->request('DELETE', '/currencies/' . $currency->getId());

        $currencyAfterDelete = $entityManager->find(Currency::class, $currencyId);

        self::assertNull($currencyAfterDelete, 'The Currency has not been deleted.');
    }
}
