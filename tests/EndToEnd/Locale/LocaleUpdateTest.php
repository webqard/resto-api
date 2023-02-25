<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\ApiResource\LocaleInput;
use App\Controller\Locale\LocalePutController;
use App\Controller\SendErrorController;
use App\Entity\Locale;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\Locale\LocalePutRepository;
use App\State\Locale\LocalePutProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database crUd for the locale.
 */
#[
    PA\CoversClass(LocalePutController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocalePutProcessor::class),
    PA\UsesClass(LocalePutRepository::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_update'),
    PA\Group('locale'),
    PA\TestDox('Locale')
]
final class LocaleUpdateTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be updated in the database.
     */
    public function testIsUpdatedInTheDatabaseWithPut(): void
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

        $localeToPut = [
            'code' => 'fr_FR'
        ];
        $client->request('PUT', '/locales/' . $locale->getId(), content: json_encode($localeToPut));

        $entityManager->refresh($locale);

        self::assertSame('fr_FR', $locale->getCode());
    }
}
