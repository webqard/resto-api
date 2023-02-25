<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\Controller\Currency\CurrencyDeleteController;
use App\Entity\Currency;
use App\Repository\Currency\CurrencyDeleteRepository;
use App\Repository\Currency\CurrencyGetRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database cruD for the currency.
 */
#[
    PA\CoversClass(CurrencyDeleteController::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyDeleteRepository::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_delete'),
    PA\Group('currency'),
    PA\TestDox('Currency')
]
final class CurrencyDeleteTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be deleted from the database.
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
