<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocalePutRepository;
use App\Repository\Locale\LocaleRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale PUT repository.
 */
#[
    PA\CoversClass(LocalePutRepository::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleRepository::class),
    PA\Group('repositories'),
    PA\Group('repository_locales'),
    PA\Group('repository_locales_put'),
    PA\Group('locale')
]
final class LocalePutRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be saved.
     */
    public function testCanPutALocale(): void
    {
        $locale = new Locale('en_GB');

        static::createClient();
        $localeRepository = static::getContainer()->get(LocalePutRepository::class);
        $localeRepository->save($locale);

        self::assertSame(1, $locale->getId(), 'The locale must have an identifier.');
        self::assertSame('en_GB', $locale->getCode());
    }
}
