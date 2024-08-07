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
use App\Entity\User;
use App\Repository\Locale\LocalePostRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Locale\LocalePostProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the locale POST.
 */
#[
    PA\CoversClass(LocalePostController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocalePostProcessor::class),
    PA\UsesClass(LocalePostRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\UsesClass(Violation::class),
    PA\UsesClass(Violations::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_post'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocalePostTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a locale
     * needs authentication to be posted.
     */
    public function testNeedsAuthenticationToBePosted(): void
    {
        $client = static::createClient();

        $client->request('POST', '/locales');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a locale
     * can not be posted
     * without ROLE_POST_LOCALE.
     */
    public function testCanNotBePostedWithoutRolePostLocale(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('POST', '/locales');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_POST_LOCALE.
     */
    private function addAUserWithRolePostLocale(): void
    {
        $this->addAUserWithRole('ROLE_POST_LOCALE');
    }

    /**
     * Tests that a locale can be created.
     */
    public function testCanBePosted(): void
    {
        $client = static::createClient();

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $locale = [
            'code' => 'en_GB'
        ];

        $client->jsonRequest('POST', '/locales', $locale);
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
    public function testCanNotBePostedWithACodeThatAlreadyExist(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $sameLocale = [
            'code' => 'en_GB'
        ];

        $client->jsonRequest('POST', '/locales', $sameLocale);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'POST same locale twice did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code already exist.', $jsonResponse[0]->message);
    }


    /**
     * Tests that a locale can not be created
     * from invalid json.
     */
    public function testCanNotBePostedWithInvalidJson(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $client->request('POST', '/locales', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid json.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Invalid json.', $jsonResponse->message,);
    }


    /**
     * Tests that an empty body request
     * can not create a locale.
     */
    public function testCanNotBePostedWithAnEmptyBodyRequest(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $client->jsonRequest('POST', '/locales');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed with an empty body request.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Properties are missing.', $jsonResponse->message,);
    }


    /**
     * Return invalid type codes.
     * @return array invalid type codes.
     */
    public static function getInvalidTypeCodes(): array
    {
        return [
            'null' => [null],
            'an integer (5)' => [5],
            'an array ([])' => [[]]
        ];
    }

    /**
     * Tests that an invalid type code
     * can not be created.
     * @param mixed $invalidTypeCode an invalid type code.
     */
    #[
        PA\DataProvider('getInvalidTypeCodes'),
        PA\TestDox('Can not be posted when the code is $_dataName')
    ]
    public function testCanNotBePostedWithAnInvalidTypeCode(mixed $invalidTypeCode): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $locale = [
            'code' => $invalidTypeCode
        ];

        $client->jsonRequest('POST', '/locales', $locale);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Type error.', $jsonResponse->message,);
    }


    /**
     * Tests that an invalid code
     * can not be created.
     */
    public function testCanNotBePostedWithAnInvalidCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $locale = [
            'code' => 'aaa_AAA_aaa'
        ];

        $client->jsonRequest('POST', '/locales', $locale);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code is invalid.', $jsonResponse[0]->message);
    }


    /**
     * Tests that a locale can not be created
     * with a blank code.
     */
    public function testCanNotBePostedWithABlankCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostLocale();
        $this->authenticateClient($client);

        $locale = [
            'code' => ''
        ];

        $client->jsonRequest('POST', '/locales', $locale);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST did not failed for invalid code.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('code', $jsonResponse[0]->property);
        self::assertSame('The code is blank.', $jsonResponse[0]->message);
    }
}
