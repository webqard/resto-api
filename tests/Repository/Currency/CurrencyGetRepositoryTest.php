<?php

declare(strict_types=1);

namespace App\Tests\Repository\Currency;

use App\Entity\Currency;
use App\Repository\Currency\CurrencyGetRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency GET repository.
 */
#[
    PA\CoversClass(CurrencyGetRepository::class),
    PA\UsesClass(Currency::class),
    PA\Group('repositories'),
    PA\Group('repository_currencies'),
    PA\Group('repository_currencies_get'),
    PA\Group('currency')
]
final class CurrencyGetRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be found.
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

        self::assertSame(1, $foundCurrency->getId(), 'The currency must have an identifier.');
        self::assertSame('EUR', $foundCurrency->getCode());
        self::assertSame(2, $foundCurrency->getDecimals());
    }
}
