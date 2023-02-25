<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\ApiResource\CurrencyInput;
use App\ApiResource\ResourceLink;
use App\Controller\Currency\CurrencyPostController;
use App\Controller\SendErrorController;
use App\Entity\Currency;
use App\Repository\Currency\CurrencyPostRepository;
use App\State\Currency\CurrencyPostProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database Crud for the currency.
 */
#[
    PA\CoversClass(CurrencyPostController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyInput::class),
    PA\UsesClass(CurrencyPostProcessor::class),
    PA\UsesClass(CurrencyPostRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_create'),
    PA\Group('currency'),
    PA\TestDox('Currency')
]
final class CurrencyCreateTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a currency can be created in the database.
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $currencyToPost = [
            'code' => 'EUR',
            'decimals' => 2
        ];

        $client->request('POST', '/currencies', content: json_encode($currencyToPost));
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        $currencyLink = explode('/', $jsonResponse->link);
        $currencyLinkLastKey = array_key_last($currencyLink);
        $currencyLinkId = (int)$currencyLink[$currencyLinkLastKey];

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $postedCurrency = $entityManager->find(Currency::class, $currencyLinkId);

        self::assertSame($currencyLinkId, $postedCurrency->getId());
        self::assertSame('EUR', $postedCurrency->getCode());
        self::assertSame(2, $postedCurrency->getDecimals());
    }
}
