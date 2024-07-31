<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\ApiResource\LocaleOutput;
use App\Controller\Locale\LocaleGetController;
use App\Entity\Locale;
use App\Entity\User;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\State\Locale\LocaleProvider;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the database cRud for the locale.
 */
#[
    PA\CoversClass(LocaleGetController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocaleOutput::class),
    PA\UsesClass(LocaleProvider::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_read'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocaleReadTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_GET_LOCALE.
     */
    private function addAUserWithRoleGetLocale(): void
    {
        $this->addAUserWithRole('ROLE_GET_LOCALE');
    }

    /**
     * Tests that a locale can be read from the database.
     */
    public function testIsReadFromTheDatabaseWithGet(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('GET', '/locales/' . $locale->getId());
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        self::assertSame('en_GB', $jsonResponse->code);
    }
}
