<?php

declare(strict_types=1);

namespace App\Tests\Repository\Locale;

use App\Entity\Locale;
use App\Repository\Locale\LocalePutRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale PUT repository.
 *
 * @coversDefaultClass \App\Repository\Locale\LocalePutRepository
 * @covers ::__construct
 * @group repositories
 * @group repository_locales
 * @group repository_locales_put
 * @group locale
 */
final class LocalePutRepositoryTest extends WebTestCase
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

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $localeRepository = static::getContainer()->get(LocalePutRepository::class);
        $foundLocale = $localeRepository->find($locale);

        self::assertSame(1, $foundLocale->getId(), 'The locale must have an identifier.');
        self::assertSame('en_GB', $foundLocale->getCode());
    }

    /**
     * Tests that a locale can be saved.
     *
     * @covers ::save
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
