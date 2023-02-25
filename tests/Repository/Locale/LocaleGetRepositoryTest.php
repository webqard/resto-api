<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\Locale\LocaleRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale GET repository.
 */
#[
    PA\CoversClass(LocaleGetRepository::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleRepository::class),
    PA\Group('repositories'),
    PA\Group('repository_locales'),
    PA\Group('repository_locales_get'),
    PA\Group('locale')
]
final class LocaleGetRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be found.
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
