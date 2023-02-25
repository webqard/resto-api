<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\ApiResource\CurrencyInput;
use App\Controller\Currency\CurrencyPutController;
use App\Controller\SendErrorController;
use App\Entity\Currency;
use App\Repository\Currency\CurrencyGetRepository;
use App\Repository\Currency\CurrencyPutRepository;
use App\State\Currency\CurrencyPutProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database crUd for the currency.
 */
#[
    PA\CoversClass(CurrencyPutController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(CurrencyInput::class),
    PA\UsesClass(CurrencyPutRepository::class),
    PA\UsesClass(CurrencyPutProcessor::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_update'),
    PA\Group('currency'),
    PA\TestDox('Currency')
]
final class CurrencyUpdateTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be updated in the database.
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
}
