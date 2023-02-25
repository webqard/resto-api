<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\Controller\Locale\LocaleDeleteController;
use App\Entity\Locale;
use App\Repository\Locale\LocaleDeleteRepository;
use App\Repository\Locale\LocaleGetRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database cruD for the locale.
 */
#[
    PA\CoversClass(LocaleDeleteController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleDeleteRepository::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_delete'),
    PA\Group('locale'),
    PA\TestDox('Locale')
]
final class LocaleDeleteTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be deleted from the database.
     */
    public function testIsDeletedFromTheDatabaseWithDelete(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

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
