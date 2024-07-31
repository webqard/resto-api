<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\ApiResource\CurrencyOutput;
use App\Controller\Currency\CurrencyGetController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\State\Currency\CurrencyProvider;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the database cRud for the currency.
 */
#[
    PA\CoversClass(CurrencyGetController::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyOutput::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(CurrencyProvider::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_read'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyReadTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_GET_CURRENCY.
     */
    private function addAUserWithRoleGetCurrency(): void
    {
        $this->addAUserWithRole('ROLE_GET_CURRENCY');
    }

    /**
     * Tests that a currency can be read from the database.
     */
    public function testIsReadFromTheDatabaseWithGet(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetCurrency();
        $this->authenticateClient($client);

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
