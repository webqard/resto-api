<?php

declare(strict_types=1);

namespace App\Tests\Api\Currency;

use App\Entity\Currency;
use App\Repository\Currency\CurrencyDeleteRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the currency DELETE repository.
 *
 * @coversDefaultClass \App\Repository\Currency\CurrencyDeleteRepository
 * @covers ::__construct
 * @covers ::delete
 * @group repositories
 * @group repository_currencys
 * @group repository_currencys_delete
 * @group currency
 */
final class CurrencyDeleteRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be deleted.
     *
     * @uses \App\Entity\Currency::__construct
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
