<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\ApiResource\ApiResponse;
use App\Controller\Locale\LocaleDeleteController;
use App\Entity\Locale;
use App\Repository\Locale\LocaleDeleteRepository;
use App\Repository\Locale\LocaleGetRepository;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale DELETE.
 */
#[
    PA\CoversClass(LocaleDeleteController::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleDeleteRepository::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_delete'),
    PA\Group('locale')
]
final class LocaleDeleteTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be deleted.
     */
    public function testCanDeleteALocale(): void
    {
        $client = static::createClient();
        $locale = new Locale('en_GB');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('DELETE', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(204, 'DELETE "/locales/1" failed.');
        self::assertEmpty($apiResponse, 'The response must not have a body.');
    }


    /**
     * Tests that a locale can not be deleted
     * from an non existant identifier.
     */
    public function testCanNotDeleteALocaleFromAnNonExistantId(): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $client->request('DELETE', '/locales/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'DELETE "/locales/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}
