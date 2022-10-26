<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocaleDeleteRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale DELETE repository.
 *
 * @coversDefaultClass \App\Repository\Locale\LocaleDeleteRepository
 * @covers ::__construct
 * @covers ::delete
 * @group repositories
 * @group repository_locales
 * @group repository_locales_delete
 * @group locale
 */
final class LocaleDeleteRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be deleted.
     *
     * @uses \App\Entity\Locale::__construct
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
