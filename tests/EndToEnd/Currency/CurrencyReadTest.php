<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\ApiResource\CurrencyOutput;
use App\Controller\Currency\CurrencyGetController;
use App\Entity\Currency;
use App\Repository\Currency\CurrencyGetRepository;
use App\State\Currency\CurrencyProvider;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database cRud for the currency.
 */
#[
    PA\CoversClass(CurrencyGetController::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyOutput::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(CurrencyProvider::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_read'),
    PA\Group('currency'),
    PA\TestDox('Currency')
]
final class CurrencyReadTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be read from the database.
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
}
