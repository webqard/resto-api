<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\Entity\Locale;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale DELETE.
 *
 * @coversDefaultClass \App\Controller\Locale\LocaleDeleteController
 * @covers ::__construct
 * @covers ::delete
 * @uses \App\Repository\LocaleRepository::__construct
 * @group api
 * @group api_locales
 * @group api_locales_delete
 */
final class LocaleDeleteTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be deleted.
     *
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Repository\LocaleRepository::delete
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

        $deletedLocale = $entityManager->find(Locale::class, 1);

        self::assertNull($deletedLocale, 'The Locale has not been deleted.');
    }


    /**
     * Return invalid type Ids.
     * @return array invalid type Ids.
     */
    private function getInvalidTypeId(): array
    {
        return [
            'id is a string (test)' => ['test'],
            'id is a float (1.5)' => [1.5]
        ];
    }

    /**
     * Tests that a locale can not be deleted
     * from an invalid type identifier.
     * @param mixed $invalidTypeId an invalid type identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @dataProvider getInvalidTypeId
     */
    public function testCanNotDeleteALocaleFromAnInvalidTypeId(mixed $invalidTypeId): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $client->request('DELETE', '/locales/' . $invalidTypeId);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'DELETE "/locales/' . $invalidTypeId . '" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }


    /**
     * Tests that a locale can not be deleted
     * from an non existant identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
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

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }
}