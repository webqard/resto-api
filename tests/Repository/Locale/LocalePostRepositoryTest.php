<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocalePostRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale POST repository.
 */
#[
    PA\CoversClass(LocalePostRepository::class),
    PA\UsesClass(Locale::class),
    PA\Group('repositories'),
    PA\Group('repository_locales'),
    PA\Group('repository_locales_post'),
    PA\Group('locale')
]
final class LocalePostRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be saved.
     */
    public function testCanPostALocale(): void
    {
        $locale = new Locale('en_GB');

        static::createClient();
        $localeRepository = static::getContainer()->get(LocalePostRepository::class);
        $localeRepository->save($locale);

        self::assertSame(1, $locale->getId(), 'The locale must have an identifier.');
        self::assertSame('en_GB', $locale->getCode());
    }
}
