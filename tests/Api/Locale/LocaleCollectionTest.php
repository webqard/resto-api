<?php

declare(strict_types=1);

namespace App\Tests\Api\Locale;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleOutput;
use App\Controller\Locale\LocaleCollectionController;
use App\Entity\Locale;
use App\Entity\User;
use App\Entity\Property\Code;
use App\Exception\UnexpectedDirectionException;
use App\Exception\UnexpectedFieldException;
use App\Repository\Locale\LocaleCollectionRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Locale\LocaleProvider;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the locale's collection GET.
 */
#[
    PA\CoversClass(LocaleCollectionController::class),
    PA\CoversClass(LocaleCollectionRepository::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Code::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleOutput::class),
    PA\UsesClass(LocaleProvider::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(UnexpectedDirectionException::class),
    PA\UsesClass(UnexpectedFieldException::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('api'),
    PA\Group('api_locales'),
    PA\Group('api_locales_collection'),
    PA\Group('locale'),
    PA\TestDox('A locale collection')
]
final class LocaleCollectionTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a locale collection
     * needs authentication to be returned.
     */
    public function testNeedsAuthenticationToBeReturned(): void
    {
        $client = static::createClient();

        $client->request('GET', '/locales');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a locale collection
     * can not be returned
     * without ROLE_GET_LOCALE_COLLECTION.
     */
    public function testCanNotBeReturnedWithoutRoleGetLocaleCollection(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('GET', '/locales');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Generates 30 locales.
     */
    private function generate30Locales(): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        for ($localeIndex = 1; $localeIndex < 31; $localeIndex++) {
            $locale = new Locale('locale' . $localeIndex);
            $entityManager->persist($locale);
        }

        $entityManager->flush();
    }


    /**
     * Adds a user with ROLE_GET_LOCALE_COLLECTION.
     */
    private function addAUserWithRoleGetLocaleCollection(): void
    {
        $this->addAUserWithRole('ROLE_GET_LOCALE_COLLECTION');
    }

    /**
     * Tests that a collection can be returned
     * without parameter.
     */
    public function testCanBeReturnedWithoutParameter(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(30, $jsonResponse);
    }


    /**
     * Tests that an 400 HTTP response is returned
     * if a field is not queryable.
     */
    public function testReturnsA400HttpResponseIfAFieldIsNotQueryable(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $client->request('GET', '/locales?criterias[notAField]=123');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'GET "/locales?criterias[notAField]=123" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('"notAField" is not queryable. Queryable fields are : id, code.', $jsonResponse->message);
    }

    /**
     * Tests that locales can be returned.
     */
    public function testCanBeReturnedFromAPartialCode(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales?criterias[code]=ale1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales?criterias[code]=ale1" failed.');
        self::assertJson($apiResponse);

        $locales = json_decode($apiResponse, false);

        self::assertCount(11, $locales);

        foreach ($locales as $locale) {
            self::assertStringContainsString('cale1', $locale->code);
        }
    }


    /**
     * Tests that an 400 HTTP response is returned
     * if the field is not orderable.
     */
    public function testReturnsA400HttpResponseIfTheFieldIsNotOrderable(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $client->request('GET', '/locales?orderBy[notAField]=ASC');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'GET "/locales?orderBy[notAField]=ASC" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('"notAField" is not orderable. Orderable fields are : id, code.', $jsonResponse->message);
    }

    /**
     * Tests that an 400 HTTP response is returned
     * if the direction is not valid.
     */
    public function testReturnsA400HttpResponseIfTheDirectionIsNotValid(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $client->request('GET', '/locales?orderBy[id]=notADirection');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'GET "/locales?orderBy[id]=notADirection" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The orderable direction must be "ASC" or "DESC".', $jsonResponse->message);
    }


    /**
     * Tests that a collection can be
     * ordered by id asc.
     */
    public function testCanBeReturnedOrderedByIdAsc(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales?orderBy[id]=ASC');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales?orderBy[id]=ASC" failed.');
        self::assertJson($apiResponse);

        $locales = json_decode($apiResponse, false);

        self::assertSame(1, $locales[0]->id);
        self::assertSame(30, $locales[29]->id);
    }

    /**
     * Tests that a collection can be
     * ordered by id desc.
     */
    public function testCanBeReturnedOrderedByIdDesc(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales?orderBy[id]=DESC');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales?orderBy[id]=DESC" failed.');
        self::assertJson($apiResponse);

        $locales = json_decode($apiResponse, false);

        self::assertSame(30, $locales[0]->id);
        self::assertSame(1, $locales[29]->id);
    }


    /**
     * Tests that a collection can be
     * ordered by code asc.
     */
    public function testCanBeReturnedOrderedByCodeAsc(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales?orderBy[code]=ASC');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales?orderBy[code]=ASC" failed.');
        self::assertJson($apiResponse);

        $locales = json_decode($apiResponse, false);

        self::assertSame('locale1', $locales[0]->code);
        self::assertSame('locale9', $locales[29]->code);
    }

    /**
     * Tests that a collection can be
     * ordered by code desc.
     */
    public function testCanBeReturnedOrderedByCodeDesc(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales?orderBy[code]=DESC');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales?orderBy[code]=DESC" failed.');
        self::assertJson($apiResponse);

        $locales = json_decode($apiResponse, false);

        self::assertSame('locale9', $locales[0]->code);
        self::assertSame('locale1', $locales[29]->code);
    }


    /**
     * Tests that an 400 HTTP response is returned
     * if the limit is negative.
     */
    public function testReturnsA400HttpResponseIfTheLimitIsNegative(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $client->request('GET', '/locales?limit=-1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'GET "/locales?limit=-1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The limit is negative.', $jsonResponse->message);
    }

    /**
     * Tests that a collection can be returned
     * with a specified quantity.
     */
    public function testCanBeReturnedWithALimitedCollection(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales?limit=10');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales?limit=10" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(10, $jsonResponse);
    }


    /**
     * Tests that an 400 HTTP response is returned
     * if the offset is negative.
     */
    public function testReturnsA400HttpResponseIfTheOffsetIsNegative(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $client->request('GET', '/locales?offset=-1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'GET "/locales?offset=-1" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The offset is negative.', $jsonResponse->message);
    }

    /**
     * Tests that a collection can be returned
     * from a specific offset.
     */
    public function testCanBeReturnedFromASpecificOffset(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetLocaleCollection();
        $this->authenticateClient($client);

        $this->generate30Locales();

        $client->request('GET', '/locales?offset=5');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200, 'GET "/locales?offset=5" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame(6, $jsonResponse[0]->id);
    }
}
