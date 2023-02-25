<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd\Locale;

use App\ApiResource\LocaleInput;
use App\ApiResource\ResourceLink;
use App\Controller\Locale\LocalePostController;
use App\Controller\SendErrorController;
use App\Entity\Locale;
use App\Repository\Locale\LocalePostRepository;
use App\State\Locale\LocalePostProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the database Crud for the locale.
 */
#[
    PA\CoversClass(LocalePostController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocalePostProcessor::class),
    PA\UsesClass(LocalePostRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\Group('e2e'),
    PA\Group('e2e_locale'),
    PA\Group('e2e_locale_create'),
    PA\Group('locale'),
    PA\TestDox('Locale')
]
final class LocaleCreateTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be created in the database.
     */
    public function testIsCreatedInTheDatabaseWithPost(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $localeToPost = [
            'code' => 'en_GB'
        ];

        $client->request('POST', '/locales', content: json_encode($localeToPost));
        $jsonResponse = json_decode($client->getResponse()->getContent(), false);

        $localeLink = explode('/', $jsonResponse->link);
        $localeLinkLastKey = array_key_last($localeLink);
        $localeLinkId = (int)$localeLink[$localeLinkLastKey];

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $postedLocale = $entityManager->find(Locale::class, $localeLinkId);

        self::assertSame($localeLinkId, $postedLocale->getId());
        self::assertSame('en_GB', $postedLocale->getCode());
    }
}
