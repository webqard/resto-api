<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\Controller\Currency\CurrencyDeleteController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyDeleteRepository;
use App\Repository\Currency\CurrencyGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the database cruD for the currency.
 */
#[
    PA\CoversClass(CurrencyDeleteController::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyDeleteRepository::class),
    PA\UsesClass(CurrencyGetRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_delete'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyDeleteTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_DELETE_CURRENCY.
     */
    private function addAUserWithRoleDeleteCurrency(): void
    {
        $this->addAUserWithRole('ROLE_DELETE_CURRENCY');
    }

    /**
     * Tests that a currency can be deleted from the database.
     */
    public function testIsDeletedFromTheDatabaseWithDelete(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleDeleteCurrency();
        $this->authenticateClient($client);

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
