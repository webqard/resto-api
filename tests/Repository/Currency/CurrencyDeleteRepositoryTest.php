<?php

declare(strict_types=1);

namespace App\Tests\Repository\Currency;

use App\Entity\Currency;
use App\Repository\Currency\CurrencyDeleteRepository;
use App\Repository\Currency\CurrencyRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency DELETE repository.
 */
#[
    PA\CoversClass(CurrencyDeleteRepository::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyRepository::class),
    PA\Group('repositories'),
    PA\Group('repository_currencies'),
    PA\Group('repository_currencies_delete'),
    PA\Group('currency')
]
final class CurrencyDeleteRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be deleted.
     */
    public function testCanDeleteACurrency(): void
    {
        static::createClient();
        $currency = new Currency('EUR', 2);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyRepository = static::getContainer()->get(CurrencyDeleteRepository::class);
        $currencyRepository->delete($currency);

        $deletedCurrency = $entityManager->find(Currency::class, 1);

        self::assertNull($deletedCurrency, 'The Currency has not been deleted.');
    }
}
