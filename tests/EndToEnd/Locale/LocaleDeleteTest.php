<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\Controller\Locale\LocaleDeleteController;
use App\Entity\Locale;
use App\Entity\User;
use App\Repository\Locale\LocaleDeleteRepository;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the database cruD for the locale.
 */
#[
    PA\CoversClass(LocaleDeleteController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleDeleteRepository::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_delete'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocaleDeleteTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_DELETE_LOCALE.
     */
    private function addAUserWithRoleDeleteLocale(): void
    {
        $this->addAUserWithRole('ROLE_DELETE_LOCALE');
    }

    /**
     * Tests that a locale can be deleted from the database.
     */
    public function testIsDeletedFromTheDatabaseWithDelete(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleDeleteLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();
        $localeId = $locale->getId();

        $client->request('DELETE', '/locales/' . $locale->getId());

        $localeAfterDelete = $entityManager->find(Locale::class, $localeId);

        self::assertNull($localeAfterDelete, 'The Locale has not been deleted.');
    }
}
