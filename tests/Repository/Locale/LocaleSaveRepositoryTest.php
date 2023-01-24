<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocaleSaveRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale save repository.
 *
 * @coversDefaultClass \App\Repository\Locale\LocaleSaveRepository
 * @covers ::__construct
 * @covers ::save
 * @group repositories
 * @group repository_locales
 * @group repository_locales_post
 * @group locale
 */
final class LocaleSaveRepositoryTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be saved.
     *
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Property\Code::getCode
     */
    public function testCanPostALocale(): void
    {
        $locale = new Locale('en_GB');

        static::createClient();
        $localeRepository = static::getContainer()->get(LocaleSaveRepository::class);
        $localeRepository->save($locale);

        self::assertSame(1, $locale->getId(), 'The locale must have an identifier.');
        self::assertSame('en_GB', $locale->getCode());
    }
}
