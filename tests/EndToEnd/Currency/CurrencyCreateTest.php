<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Currency;

use App\ApiResource\CurrencyInput;
use App\ApiResource\ResourceLink;
use App\Controller\Currency\CurrencyPostController;
use App\Controller\SendErrorController;
use App\Entity\Currency;
use App\Entity\User;
use App\Repository\Currency\CurrencyPostRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\State\Currency\CurrencyPostProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

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
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_currency'),
    PA\Group('e2e_currency_create'),
    PA\Group('currency'),
    PA\TestDox('A currency')
]
final class CurrencyCreateTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_POST_CURRENCY.
     */
    private function addAUserWithRolePostCurrency(): void
    {
        $this->addAUserWithRole('ROLE_POST_CURRENCY');
    }

    /**
     * Tests that a currency can be created in the database.
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostCurrency();
        $this->authenticateClient($client);

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
