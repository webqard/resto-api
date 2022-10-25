<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\Entity\Currency;
use App\Repository\Currency\CurrencyGetRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency GET repository.
 *
 * @coversDefaultClass \App\Repository\Currency\CurrencyGetRepository
 * @covers ::__construct
 * @group repositories
 * @group repository_currencies
 * @group repository_currencies_get
 * @group currency
 */
final class CurrencyGetRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be deleted.
     *
     * @uses \App\Entity\Currency::__construct
     */
    public function testCanFindACurrency(): void
    {
        static::createClient();
        $currency = new Currency('EUR', 2);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyRepository = static::getContainer()->get(CurrencyGetRepository::class);
        $foundCurrency = $currencyRepository->find(1);

        self::assertInstanceOf(Currency::class, $foundCurrency);
    }
}
