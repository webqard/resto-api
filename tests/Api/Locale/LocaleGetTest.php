<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleOutput;
use App\Controller\Locale\LocaleGetController;
use App\Entity\Locale;
use App\Repository\Locale\LocaleGetRepository;
use App\State\Locale\LocaleProvider;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale GET.
 */
#[
    PA\CoversClass(LocaleGetController::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocaleOutput::class),
    PA\UsesClass(LocaleProvider::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_get'),
    PA\Group('locale')
]
final class LocaleGetTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be returned.
     */
    public function testCanGetALocale(): void
    {
        $client = static::createClient();
        $locale = new Locale('en_GB');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('GET', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales/1" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('en_GB', $jsonResponse->code);
    }


    /**
     * Tests that a locale can not be returned
     * from an non existant identifier.
     */
    public function testCanNotGetALocaleFromAnNonExistantId(): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $client->request('GET', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'GET "/locales/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}
