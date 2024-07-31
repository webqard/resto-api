<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\ApiResource\CurrencyInput;
use App\Controller\Currency\CurrencyPutController;
use App\Controller\SendErrorController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyGetRepository;
use App\Repository\Currency\CurrencyPutRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\State\Currency\CurrencyPutProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

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
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_update'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyUpdateTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_PUT_CURRENCY.
     */
    private function addAUserWithRolePutCurrency(): void
    {
        $this->addAUserWithRole('ROLE_PUT_CURRENCY');
    }

    /**
     * Tests that a currency can be updated in the database.
     */
    public function testIsUpdatedInTheDatabaseWithPut(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutCurrency();
        $this->authenticateClient($client);

        $currency = new Currency('EUR', 2);
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($currency);
        $entityManager->flush();

        $currencyToPut = [
            'code' => 'GBP',
            'decimals' => 3
        ];
        $client->request('PUT', '/currencies/' . $currency->getId(), content: json_encode($currencyToPut));

        $savedCurrency = $entityManager->find(Currency::class, 1);

        self::assertSame('GBP', $savedCurrency->getCode());
        self::assertSame(3, $savedCurrency->getDecimals());
    }
}
