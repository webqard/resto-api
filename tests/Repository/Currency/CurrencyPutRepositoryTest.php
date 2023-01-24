<?php

declare(strict_types=1);

namespace App\Tests\Repository\Currency;

use App\Entity\Currency;
use App\Repository\Currency\CurrencyPutRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency PUT repository.
 *
 * @coversDefaultClass \App\Repository\Currency\CurrencyPutRepository
 * @covers ::__construct
 * @group repositories
 * @group repository_currencies
 * @group repository_currencies_put
 * @group currency
 */
final class CurrencyPutRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be found.
     *
     * @covers ::find
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Property\Code::getCode
     */
    public function testCanFindACurrency(): void
    {
        static::createClient();
        $currency = new Currency('EUR', 2);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyRepository = static::getContainer()->get(CurrencyPutRepository::class);
        $foundCurrency = $currencyRepository->find($currency);

        self::assertSame(1, $foundCurrency->getId(), 'The currency must have an identifier.');
        self::assertSame('EUR', $foundCurrency->getCode());
        self::assertSame(2, $foundCurrency->getDecimals());
    }

    /**
     * Tests that a currency can be saved.
     *
     * @covers ::save
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Property\Code::getCode
     */
    public function testCanSaveACurrency(): void
    {
        $currency = new Currency('EUR', 2);

        static::createClient();
        $currencyRepository = static::getContainer()->get(CurrencyPutRepository::class);
        $currencyRepository->save($currency);

        self::assertSame(1, $currency->getId(), 'The currency must have an identifier.');
        self::assertSame('EUR', $currency->getCode());
        self::assertSame(2, $currency->getDecimals());
    }
}
