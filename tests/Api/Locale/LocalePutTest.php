<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleInput;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Controller\Locale\LocalePutController;
use App\Controller\SendErrorController;
use App\Entity\Locale;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\Locale\LocalePutRepository;
use App\State\Locale\LocalePutProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale PUT.
 */
#[
    PA\CoversClass(LocalePutController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocalePutProcessor::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocalePutRepository::class),
    PA\UsesClass(Violation::class),
    PA\UsesClass(Violations::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_put'),
    PA\Group('locale')
]
final class LocalePutTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be updated.
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

        self::assertNotSame('', $jsonResponse->message, 'The error message is empty.');
    }


    /**
     * Tests that a locale can not be updated
     * from invalid json.
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

        self::assertNotSame(
            '',
            $jsonResponse->message,
            'The error message is empty.'
        );
    }


    /**
     * Tests that an empty body request
     * can not create a locale.
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
    public static function getInvalidTypeCodes(): array
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
     */
    #[
        PA\DataProvider('getInvalidTypeCodes'),
        PA\TestDox('Can not put a locale when $_dataName')
    ]
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

        self::assertNotSame(
            '',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Tests that an invalid code
     * can not be updated.
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
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }
}
