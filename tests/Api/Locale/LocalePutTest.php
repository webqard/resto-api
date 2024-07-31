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
use App\Entity\User;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\Locale\LocalePutRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Locale\LocalePutProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the locale PUT.
 */
#[
    PA\CoversClass(LocalePutController::class),
    PA\CoversClass(SendErrorController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleInput::class),
    PA\UsesClass(LocalePutProcessor::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocalePutRepository::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\UsesClass(Violation::class),
    PA\UsesClass(Violations::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_put'),
    PA\Group('locale'),
    PA\TestDox('A locale')
]
final class LocalePutTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a locale
     * needs authentication to be put.
     */
    public function testNeedsAuthenticationToBePut(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/locales/1');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a locale
     * can not be put
     * without ROLE_PUT_LOCALE.
     */
    public function testCanNotBePutWithoutRolePutLocale(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('PUT', '/locales/1');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_PUT_LOCALE.
     */
    private function addAUserWithRolePutLocale(): void
    {
        $this->addAUserWithRole('ROLE_PUT_LOCALE');
    }

    /**
     * Tests that a locale can be updated.
     */
    public function testCanBePut(): void
    {
        $client = static::createClient();

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

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
    public function testCanNotBePutWithTheCodeOfAnOtherOne(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

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
        self::assertSame('The code already exist.', $jsonResponse[0]->message);
    }


    /**
     * Tests that a locale can not be updated
     * from an non existant identifier.
     */
    public function testCanNotBePutFromAnNonExistantId(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

        $locale = [
            'code' => 'en_GB'
        ];

        $client->request('PUT', '/locales/1', content: json_encode($locale));
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404, 'PUT "/locales/1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The resource has not been found.', $jsonResponse->message);
    }


    /**
     * Tests that a locale can not be updated
     * from invalid json.
     */
    public function testCanNotBePutWithInvalidJson(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('PUT', '/locales/1', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed for invalid json.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Invalid json.', $jsonResponse->message);
    }


    /**
     * Tests that an empty body request
     * can not create a locale.
     */
    public function testCanNotBePutWithAnEmptyBodyRequest(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('PUT', '/locales/1', content: '[]');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'PUT did not failed with an empty body request.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('Properties are missing.', $jsonResponse->message);
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
     * can not be updated.
     * @param mixed $invalidTypeCode an invalid type code.
     */
    #[
        PA\DataProvider('getInvalidTypeCodes'),
        PA\TestDox('Can not be put when the code is $_dataName')
    ]
    public function testCanNotBePutWithAnInvalidTypeCode(mixed $invalidTypeCode): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

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

        self::assertSame('Type error.', $jsonResponse->message);
    }


    /**
     * Tests that a locale can not be updated
     * with a blank code.
     */
    public function testCanNotBePutWithABlankCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

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
        self::assertSame('The code is blank.', $jsonResponse[0]->message);
    }


    /**
     * Tests that an invalid code
     * can not be updated.
     */
    public function testCanNotBePutWithAnInvalidCode(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePutLocale();
        $this->authenticateClient($client);

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
        self::assertSame('The code is invalid.', $jsonResponse[0]->message);
    }
}
