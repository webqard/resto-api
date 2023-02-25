<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocaleDeleteRepository;
use App\Repository\Locale\LocaleRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale DELETE repository.
 */
#[
    PA\CoversClass(LocaleDeleteRepository::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleRepository::class),
    PA\Group('repositories'),
    PA\Group('repository_locales'),
    PA\Group('repository_locales_delete'),
    PA\Group('locale')
]
final class LocaleDeleteRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be deleted.
     */
    public function testCanDeleteALocale(): void
    {
        static::createClient();
        $locale = new Locale('en_GB');

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $localeRepository = static::getContainer()->get(LocaleDeleteRepository::class);
        $localeRepository->delete($locale);

        $deletedLocale = $entityManager->find(Locale::class, 1);

        self::assertNull($deletedLocale, 'The Locale has not been deleted.');
    }
}
