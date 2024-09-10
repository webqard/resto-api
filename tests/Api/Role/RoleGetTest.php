<?php

declare(strict_types=1);

namespace App\Tests\Api\Role;

use App\ApiResource\ApiResponse;
use App\ApiResource\LocaleOutput;
use App\ApiResource\RoleOutput;
use App\ApiResource\RoleTranslationOutput;
use App\Controller\Role\RoleGetController;
use App\Entity\Locale;
use App\Entity\Role;
use App\Entity\RoleTranslation;
use App\Entity\User;
use App\Repository\Role\RoleGetRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Role\RoleProvider;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the role GET.
 */
#[
    PA\CoversClass(RoleGetController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleOutput::class),
    PA\UsesClass(Role::class),
    PA\UsesClass(RoleGetRepository::class),
    PA\UsesClass(RoleOutput::class),
    PA\UsesClass(RoleTranslationOutput::class),
    PA\UsesClass(RoleProvider::class),
    PA\UsesClass(RoleTranslation::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\Group('api'),
    PA\Group('api_roles'),
    PA\Group('api_roles_get'),
    PA\Group('role'),
    PA\TestDox('A role')
]
final class RoleGetTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a role
     * needs authentication to be returned.
     */
    public function testNeedsAuthenticationToBeReturned(): void
    {
        $client = static::createClient();

        $client->request('GET', '/roles/1');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a role
     * can not be returned
     * without ROLE_GET_ROLE.
     */
    public function testCanNotBeReturnedWithoutRoleGetRole(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();

        $this->authenticateClient($client);

        $client->request('GET', '/roles/1');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_GET_ROLE.
     */
    private function addAUserWithRoleGetRole(): void
    {
        $this->addAUserWithRole('ROLE_GET_ROLE');
    }

    /**
     * Adds a role with translations.
     */
    public function addARoleWithTranslations(): void
    {
        $localeEN = new Locale('en_GB');
        $localeFR = new Locale('fr_FR');
        $localeDE = new Locale('de_DE');
        $role = new Role('ROLE_GET_ROLE');
        $roleTranslationEN = new RoleTranslation($role, $localeEN, 'en-description');
        $roleTranslationFR = new RoleTranslation($role, $localeFR, 'fr-description');
        $roleTranslationDE = new RoleTranslation($role, $localeDE, 'de-description');
        $role->addTranslation($roleTranslationEN);
        $role->addTranslation($roleTranslationFR);
        $role->addTranslation($roleTranslationDE);

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->persist($roleTranslationEN);
        $entityManager->persist($roleTranslationFR);
        $entityManager->persist($roleTranslationDE);
        $entityManager->persist($localeEN);
        $entityManager->persist($localeFR);
        $entityManager->persist($localeDE);
        $entityManager->flush();
    }

    /**
     * Tests that a role can be returned filtered
     * for no translation.
     */
    public function testCanBeReturnedWithFilterForNoTranslation(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $this->addARoleWithTranslations();

        $client->request('GET', '/roles/1?translations[]=');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, true);

        self::assertArrayHasKey('name', $jsonResponse);
        self::assertSame('ROLE_GET_ROLE', $jsonResponse['name']);
        self::assertArrayNotHasKey('translations', $jsonResponse);
    }

    /**
     * Tests that a role can be returned filtered
     * for a translation.
     */
    public function testCanBeReturnedWithFilterForATranslation(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $this->addARoleWithTranslations();

        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'fr-FR',
        ];
        $client->request('GET', '/roles/1?translations[]=fr_FR', server: $server);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('ROLE_GET_ROLE', $jsonResponse->name);
        self::assertCount(1, $jsonResponse->translations);
        self::assertArrayHasKey(0, $jsonResponse->translations);

        $translationFR = $jsonResponse->translations[0];
        self::assertSame(2, $translationFR->id);
        self::assertSame('fr-description', $translationFR->description);
        self::assertSame(2, $translationFR->locale->id);
        self::assertSame('fr_FR', $translationFR->locale->code);
    }

    /**
     * Tests that a role can be returned filtered
     * for many translations.
     */
    public function testCanBeReturnedWithFilterForManyTranslations(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $this->addARoleWithTranslations();

        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'fr-FR',
        ];
        $client->request('GET', '/roles/1?translations[]=fr_FR&translations[]=de_DE', server: $server);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('ROLE_GET_ROLE', $jsonResponse->name);
        self::assertCount(2, $jsonResponse->translations);
        self::assertArrayHasKey(0, $jsonResponse->translations);
        self::assertArrayHasKey(1, $jsonResponse->translations);

        $translationFR = $jsonResponse->translations[0];
        self::assertSame(2, $translationFR->id);
        self::assertSame('fr-description', $translationFR->description);
        self::assertSame(2, $translationFR->locale->id);
        self::assertSame('fr_FR', $translationFR->locale->code);

        $translationDE = $jsonResponse->translations[1];
        self::assertSame(3, $translationDE->id);
        self::assertSame('de-description', $translationDE->description);
        self::assertSame(3, $translationDE->locale->id);
        self::assertSame('de_DE', $translationDE->locale->code);
    }


    /**
     * Tests that a role can be returned
     * with all translations.
     */
    public function testCanBeReturnedWithAllTranslations(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $this->addARoleWithTranslations();

        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'fr-FR',
        ];
        $client->request('GET', '/roles/1', server: $server);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('ROLE_GET_ROLE', $jsonResponse->name);
        self::assertCount(3, $jsonResponse->translations);
        self::assertArrayHasKey(0, $jsonResponse->translations);
        self::assertArrayHasKey(1, $jsonResponse->translations);
        self::assertArrayHasKey(2, $jsonResponse->translations);

        $translationEN = $jsonResponse->translations[0];
        self::assertSame(1, $translationEN->id);
        self::assertSame('en-description', $translationEN->description);
        self::assertSame(1, $translationEN->locale->id);
        self::assertSame('en_GB', $translationEN->locale->code);

        $translationFR = $jsonResponse->translations[1];
        self::assertSame(2, $translationFR->id);
        self::assertSame('fr-description', $translationFR->description);
        self::assertSame(2, $translationFR->locale->id);
        self::assertSame('fr_FR', $translationFR->locale->code);

        $translationDE = $jsonResponse->translations[2];
        self::assertSame(3, $translationDE->id);
        self::assertSame('de-description', $translationDE->description);
        self::assertSame(3, $translationDE->locale->id);
        self::assertSame('de_DE', $translationDE->locale->code);
    }


    /**
     * Tests that a role can not be returned
     * from an non existant identifier.
     */
    public function testCanNotBeReturnedFromAnNonExistantId(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $client->request('GET', '/roles/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(404);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('The resource has not been found.', $jsonResponse->message);
    }
}
