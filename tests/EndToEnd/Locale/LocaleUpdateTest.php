<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\ApiResource\LocaleInput;
use App\Controller\Locale\LocalePutController;
use App\Controller\SendErrorController;
use App\Entity\Locale;
use App\Entity\User;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\Locale\LocalePutRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\State\Locale\LocalePutProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the database crUd for the locale.
 */
#[
    PA\CoversClass(LocalePutController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocalePutProcessor::class),
    PA\UsesClass(LocalePutRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_update'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocaleUpdateTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_PUT_LOCALE.
     */
    private function addAUserWithRolePutLocale(): void
    {
        $this->addAUserWithRole('ROLE_PUT_LOCALE');
    }

    /**
     * Tests that a locale can be updated in the database.
     */
    public function testIsUpdatedInTheDatabaseWithPut(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $localeToPut = [
            'code' => 'fr_FR'
        ];
        $client->request('PUT', '/locales/' . $locale->getId(), content: json_encode($localeToPut));

        $savedLocale = $entityManager->find(Locale::class, $locale);

        self::assertSame('fr_FR', $savedLocale->getCode());
    }
}
