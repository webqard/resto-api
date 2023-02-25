<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\ApiResource\LocaleOutput;
use App\Controller\Locale\LocaleGetController;
use App\Entity\Locale;
use App\Repository\Locale\LocaleGetRepository;
use App\State\Locale\LocaleProvider;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database cRud for the locale.
 */
#[
    PA\CoversClass(LocaleGetController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocaleOutput::class),
    PA\UsesClass(LocaleProvider::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_read'),
    PA\Group('locale'),
    PA\TestDox('Locale')
]
final class LocaleReadTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be read from the database.
     */
    public function testIsReadFromTheDatabaseWithGet(): void
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

        $client->request('GET', '/locales/' . $locale->getId());
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        self::assertSame('en_GB', $jsonResponse->code);
    }
}
