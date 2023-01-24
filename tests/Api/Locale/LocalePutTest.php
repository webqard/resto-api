<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\Entity\Locale;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale PUT.
 *
 * @coversDefaultClass \App\Controller\Locale\LocalePutController
 * @covers ::__construct
 * @covers ::put
 * @uses \App\Repository\Locale\LocaleSaveRepository::__construct
 * @group api
 * @group api_locales
 * @group api_locales_put
 * @group locale
 */
final class LocalePutTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be updated.
     *
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Property\Code::getCode
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\Repository\Locale\LocaleSaveRepository::save
     * @uses \App\State\Locale\LocalePutProcessor::getEntity
     */
    public function testCanPutALocale(): void
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

        $client->request('PUT', '/locales/1', content: json_encode($localeToPut));

        self::assertResponseStatusCodeSame(204, 'PUT to "/locales/1" failed.');

        $apiResponse = $client->getResponse()->getContent();

        self::assertEmpty($apiResponse, 'The PUT success must not return a body.');
    }

    /**
     * Tests that a locale can not be updated
     * with the code of an other one.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Controller\Locale\LocalePutController::sendViolations
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Property\Code::setCode
     * @uses \App\State\Locale\LocalePutProcessor::getEntity
     */
    public function testCanNotPutALocaleWithTheCodeOfAnOtherOne(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);
        $localeEN = new Locale('en_GB');
        $localeFR = new Locale('fr_FR');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($localeEN);
        $entityManager->persist($localeFR);
        $entityManager->flush();

        $sameLocale = json_encode([
            'code' => 'fr_FR'
        ]);

        $client->request('PUT', '/locales/1', content: $sameLocale);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'PUT same locale twice did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Tests that a locale can not be updated
     * from an non existant identifier.
     *
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     */
    public function testCanNotPutALocaleFromAnNonExistantId(): void
    {
        $requestParameters = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($requestParameters);

        $locale = [
            'code' => 'en_GB'
        ];

        $client->request('PUT', '/locales/1', content: json_encode($locale));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'PUT "/locales/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }


    /**
     * Tests that a locale can not be updated
     * from invalid json.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     */
    public function testCanNotPutInvalidJson(): void
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

        $client->request('PUT', '/locales/1', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed for invalid json.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame(
            '',
            $jsonResponse->message,
            'The error message is empty.'
        );
    }


    /**
     * Tests that an empty body request
     * can not create a locale.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     */
    public function testCanNotPutAnEmptyBodyRequest(): void
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

        $client->request('PUT', '/locales/1', content: '[]');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed with an empty body request.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame(
            '',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Return invalid type codes.
     * @return array invalid type codes.
     */
    private function getInvalidTypeCodes(): array
    {
        return [
            'code is null' => [null],
            'code is an integer (5)' => [5],
            'code is an array ([])' => [[]]
        ];
    }

    /**
     * Tests that an invalid type code
     * can not be updated.
     * @param mixed $invalidTypeCode an invalid type code.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     * @dataProvider getInvalidTypeCodes
     */
    public function testCanNotPutAnInvalidTypeCode(mixed $invalidTypeCode): void
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

        $localeWithInvalidTypeCode = [
            'code' => $invalidTypeCode
        ];

        $client->request('PUT', '/locales/1', content: json_encode($localeWithInvalidTypeCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('message', $jsonResponse);
        self::assertNotSame(
            '',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Tests that an invalid code
     * can not be updated.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     */
    public function testCanNotPutAnInvalidCode(): void
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

        $localeWithInvalidCode = [
            'code' => 'aaa_AAA_aaa'
        ];

        $client->request('PUT', '/locales/1', content: json_encode($localeWithInvalidCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertObjectHasAttribute('property', $jsonResponse[0]);
        self::assertObjectHasAttribute('message', $jsonResponse[0]);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Tests that a locale can not be updated
     * with a blank code.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     */
    public function testCanNotPutABlankCode(): void
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

        $localeWithABlankCode = [
            'code' => ''
        ];

        $client->request('PUT', '/locales/1', content: json_encode($localeWithABlankCode));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'PUT did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertObjectHasAttribute('property', $jsonResponse[0]);
        self::assertObjectHasAttribute('message', $jsonResponse[0]);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }
}
