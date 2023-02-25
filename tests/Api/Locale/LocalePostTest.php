<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleInput;
use App\ApiResource\ResourceLink;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Controller\Locale\LocalePostController;
use App\Controller\SendErrorController;
use App\Entity\Locale;
use App\Repository\Locale\LocalePostRepository;
use App\State\Locale\LocalePostProcessor;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the locale POST.
 */
#[
    PA\CoversClass(LocalePostController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocalePostProcessor::class),
    PA\UsesClass(LocalePostRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\UsesClass(Violation::class),
    PA\UsesClass(Violations::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_post'),
    PA\Group('locale')
]
final class LocalePostTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that a locale can be created.
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

        self::assertSame('/locales/1', $jsonResponse->link);
    }

    /**
     * Tests that a code of an already existing locale
     * can not be created.
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
     * can not be created.
     * @param mixed $invalidTypeCode an invalid type code.
     */
    #[
        PA\DataProvider('getInvalidTypeCodes'),
        PA\TestDox('Can not post when $_dataName')
    ]
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

        self::assertNotSame(
            '',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Tests that an invalid code
     * can not be created.
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
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertNotSame(
            '',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }
}
