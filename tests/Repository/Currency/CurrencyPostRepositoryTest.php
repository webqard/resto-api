<?php

declare(strict_types=1);

namespace App\Tests\Repository\Currency;

use App\Entity\Currency;
use App\Repository\Currency\CurrencyPostRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency POST repository.
 */
#[
    PA\CoversClass(CurrencyPostRepository::class),
    PA\UsesClass(Currency::class),
    PA\Group('repositories'),
    PA\Group('repository_currencies'),
    PA\Group('repository_currencies_post'),
    PA\Group('currency')
]
final class CurrencyPostRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be saved.
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
