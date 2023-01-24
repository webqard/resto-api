<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\Entity\Locale;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale POST.
 *
 * @coversDefaultClass \App\Controller\Locale\LocalePostController
 * @covers ::__construct
 * @covers ::post
 * @uses \App\Repository\Locale\LocaleSaveRepository::__construct
 * @group api
 * @group api_locales
 * @group api_locales_post
 * @group locale
 */
final class LocalePostTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be created.
     *
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\ApiResource\ResourceLink::__construct
     * @uses \App\ApiResource\ResourceLink::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Repository\Locale\LocaleSaveRepository::save
     * @uses \App\State\Locale\LocalePostProcessor::getEntity
     */
    public function testCanPostALocale(): void
    {
        $client = static::createClient();

        $locale = [
            'code' => 'en_GB'
        ];

        $client->request('POST', '/locales', content: json_encode($locale));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(201, 'POST to "/locales" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertObjectHasAttribute('link', $jsonResponse);
        self::assertSame('/locales/1', $jsonResponse->link);
    }

    /**
     * Tests that a code of an already existing locale
     * can not be created.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\LocaleInput::getCode
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     * @uses \App\Controller\Locale\LocalePostController::sendViolations
     * @uses \App\Entity\Locale::__construct
     * @uses \App\State\Locale\LocalePostProcessor::getEntity
     */
    public function testCanNotPostACodeThatAlreadyExist(): void
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

        $sameLocale = json_encode([
            'code' => 'en_GB'
        ]);

        $client->request('POST', '/locales', content: $sameLocale);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'POST same locale twice did not failed.');
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
     * Tests that a locale can not be created
     * from invalid json.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     */
    public function testCanNotPostInvalidJson(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $client->request('POST', '/locales', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid json.');
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
     */
    public function testCanNotPostAnEmptyBodyRequest(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $client->request('POST', '/locales', content: '[]');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed with an empty body request.');
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
     * can not be created.
     * @param mixed $invalidTypeCode an invalid type code.
     *
     * @covers ::respondToBadRequest
     * @uses \App\ApiResource\ApiResponse::__construct
     * @uses \App\ApiResource\ApiResponse::jsonSerialize
     * @dataProvider getInvalidTypeCodes
     */
    public function testCanNotPostAnInvalidTypeCode(mixed $invalidTypeCode): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $locale = [
            'code' => $invalidTypeCode
        ];

        $client->request('POST', '/locales', content: json_encode($locale));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid code.');
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
     * can not be created.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     */
    public function testCanNotPostAnInvalidCode(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $locale = [
            'code' => 'aaa_AAA_aaa'
        ];

        $client->request('POST', '/locales', content: json_encode($locale));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
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
     * Tests that a locale can not be created
     * with a blank code.
     *
     * @covers ::sendViolations
     * @uses \App\ApiResource\LocaleInput::__construct
     * @uses \App\ApiResource\Violation::__construct
     * @uses \App\ApiResource\Violation::jsonSerialize
     * @uses \App\ApiResource\Violations::__construct
     * @uses \App\ApiResource\Violations::add
     * @uses \App\ApiResource\Violations::jsonSerialize
     */
    public function testCanNotPostABlankCode(): void
    {
        $params = [
            'headers' => [
                'Accept-Language' => 'en-GB',
            ],
        ];
        $client = static::createClient($params);

        $locale = [
            'code' => ''
        ];

        $client->request('POST', '/locales', content: json_encode($locale));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
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
