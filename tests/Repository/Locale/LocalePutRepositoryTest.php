<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocalePutRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale POST repository.
 *
 * @coversDefaultClass \App\Repository\Locale\LocalePutRepository
 * @covers ::__construct
 * @covers ::save
 * @group repositories
 * @group repository_locales
 * @group repository_locales_put
 * @group locale
 */
final class LocalePutRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be saved.
     *
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Property\Code::getCode
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
