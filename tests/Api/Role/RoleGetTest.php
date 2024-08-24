<?php

declare(strict_types=1);

namespace App\Tests\Api\Role;

use App\ApiResource\ApiResponse;
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
     * Adds an EN locale.
     */
    private function addAnENLocale(): void
    {
        $container = self::getContainer();

        $locale = new Locale('en_GB');

        $manager = $container->get('doctrine')->getManager();

        $manager->persist($locale);
        $manager->flush();
    }

    /**
     * Adds a FR locale.
     */
    private function addAFRLocale(): void
    {
        $container = self::getContainer();

        $locale = new Locale('fr_FR');

        $manager = $container->get('doctrine')->getManager();

        $manager->persist($locale);
        $manager->flush();
    }

    /**
     * Tests that a role can be returned
     * without translation.
     */
    public function testCanBeReturnedWithoutTranslation(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $role = new Role('name');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->flush();

        $client->request('GET', '/roles/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('name', $jsonResponse->name);
        self::assertSame([], $jsonResponse->translations);
    }

    /**
     * Tests that a role can be returned
     * with a translation.
     */
    public function testCanBeReturnedWithATranslation(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $locale = new Locale('en_GB');
        $role = new Role('name');
        $roleTranslationEN = new RoleTranslation($role, $locale, 'description');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->persist($roleTranslationEN);
        $entityManager->persist($locale);
        $entityManager->flush();

        $client->request('GET', '/roles/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('name', $jsonResponse->name);
        self::assertCount(1, $jsonResponse->translations);
        self::assertArrayHasKey(0, $jsonResponse->translations);
        self::assertSame(1, $jsonResponse->translations[0]->localeId);
        self::assertSame(1, $jsonResponse->translations[0]->id);
        self::assertSame('description', $jsonResponse->translations[0]->description);
    }

    /**
     * Tests that a role can be returned
     * with many translations.
     */
    public function testCanBeReturnedWithManyTranslations(): void
    {
        $client = static::createClient();

        $this->addAUserWithRoleGetRole();
        $this->authenticateClient($client);

        $localeEN = new Locale('en_GB');
        $localeFR = new Locale('fr_FR');
        $role = new Role('name');
        $roleTranslationEN = new RoleTranslation($role, $localeEN, 'en-description');
        $roleTranslationFR = new RoleTranslation($role, $localeFR, 'fr-description');

        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($role);
        $entityManager->persist($roleTranslationEN);
        $entityManager->persist($roleTranslationFR);
        $entityManager->persist($localeEN);
        $entityManager->persist($localeFR);
        $entityManager->flush();

        $client->request('GET', '/roles/1');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('name', $jsonResponse->name);
        self::assertCount(2, $jsonResponse->translations);
        self::assertArrayHasKey(0, $jsonResponse->translations);
        self::assertArrayHasKey(1, $jsonResponse->translations);

        $translationEN = $jsonResponse->translations[0];
        self::assertSame(1, $translationEN->localeId);
        self::assertSame(1, $translationEN->id);
        self::assertSame('en-description', $translationEN->description);

        $translationFR = $jsonResponse->translations[1];
        self::assertSame(2, $translationFR->localeId);
        self::assertSame(2, $translationFR->id);
        self::assertSame('fr-description', $translationFR->description);
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
