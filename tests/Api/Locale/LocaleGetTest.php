<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\Entity\Locale;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale GET.
 *
 * @coversDefaultClass \App\Controller\Locale\LocaleGetController
 * @covers ::__construct
 * @covers ::get
 * @uses \App\Repository\Locale\LocaleGetRepository::__construct
 * @group api
 * @group api_locales
 * @group api_locales_get
 * @group locale
 */
final class LocaleGetTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be returned.
     *
     * @uses \App\ApiResource\LocaleOutput::__construct
     * @uses \App\ApiResource\LocaleOutput::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Locale::getCode
     * @uses \App\State\Locale\LocaleProvider::provideLocaleOutput
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

        self::assertObjectHasAttribute('code', $jsonResponse);
        self::assertSame('en_GB', $jsonResponse->code);
    }


    /**
     * Tests that a locale can not be returned
     * from an non existant identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
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

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}
