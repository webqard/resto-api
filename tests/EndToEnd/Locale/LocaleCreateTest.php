<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\ApiResource\LocaleInput;
use App\ApiResource\ResourceLink;
use App\Controller\Locale\LocalePostController;
use App\Controller\SendErrorController;
use App\Entity\Locale;
use App\Entity\User;
use App\Repository\Locale\LocalePostRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\UserChecker;
use App\State\Locale\LocalePostProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the database Crud for the locale.
 */
#[
    PA\CoversClass(LocalePostController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocalePostProcessor::class),
    PA\UsesClass(LocalePostRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_create'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocaleCreateTest extends JWTTestCase
{
    // Methods :

    /**
     * Adds a user with ROLE_POST_LOCALE.
     */
    private function addAUserWithRolePostLocale(): void
    {
        $this->addAUserWithRole('ROLE_POST_LOCALE');
    }

    /**
     * Tests that a locale can be created in the database.
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $localeToPost = [
            'code' => 'en_GB'
        ];

        $client->request('POST', '/locales', content: json_encode($localeToPost));
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        $localeLink = explode('/', $jsonResponse->link);
        $localeLinkLastKey = array_key_last($localeLink);
        $localeLinkId = (int)$localeLink[$localeLinkLastKey];

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $postedLocale = $entityManager->find(Locale::class, $localeLinkId);

        self::assertSame($localeLinkId, $postedLocale->getId());
        self::assertSame('en_GB', $postedLocale->getCode());
    }
}
