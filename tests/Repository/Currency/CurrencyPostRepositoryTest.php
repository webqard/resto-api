<?php

declare(strict_types=1);

namespace App\Tests\Repository\Currency;

use App\Entity\Currency;
use App\Repository\Currency\CurrencyPostRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency POST repository.
 *
 * @coversDefaultClass \App\Repository\Currency\CurrencyPostRepository
 * @covers ::__construct
 * @covers ::save
 * @group repositories
 * @group repository_currencies
 * @group repository_currencies_post
 * @group currency
 */
final class CurrencyPostRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be saved.
     *
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::getDecimals
     * @uses \App\Entity\Property\Code::getCode
     */
    public function testCanPostACurrency(): void
    {
        $currency = new Currency('EUR', 2);

        static::createClient();
        $currencyRepository = static::getContainer()->get(CurrencyPostRepository::class);
        $currencyRepository->save($currency);

        self::assertSame(1, $currency->getId(), 'The currency must have an identifier.');
        self::assertSame('EUR', $currency->getCode());
        self::assertSame(2, $currency->getDecimals());
    }
}
