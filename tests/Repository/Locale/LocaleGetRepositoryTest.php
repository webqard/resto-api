<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocaleGetRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale GET repository.
 *
 * @coversDefaultClass \App\Repository\Locale\LocaleGetRepository
 * @covers ::__construct
 * @group repositories
 * @group repository_locales
 * @group repository_locales_get
 * @group locale
 */
final class LocaleGetRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be found.
     *
     * @covers ::find
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Property\Code::getCode
     */
    public function testCanFindALocale(): void
    {
        static::createClient();
        $locale = new Locale('en_GB');

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $localeRepository = static::getContainer()->get(LocaleGetRepository::class);
        $foundLocale = $localeRepository->find(1);

        self::assertSame(1, $foundLocale->getId(), 'The locale must have an identifier.');
        self::assertSame('en_GB', $foundLocale->getCode());
    }
}
