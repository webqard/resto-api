<?php

declare(strict_types=1);

namespace App\Tests\Api\Role;

use App\ApiResource\ApiResponse;
use App\ApiResource\RoleInput;
use App\ApiResource\RoleTranslationInput;
use App\ApiResource\ResourceLink;
use App\ApiResource\Violation;
use App\ApiResource\Violations;
use App\Controller\Role\RolePostController;
use App\Controller\SendErrorController;
use App\Controller\UnprocessableTranslationController;
use App\Entity\Locale;
use App\Entity\Role;
use App\Entity\RoleTranslation;
use App\Entity\User;
use App\Exception\LocaleNotFoundException;
use App\Repository\Locale\LocaleGetRepository;
use App\Repository\Role\RolePostRepository;
use App\Repository\User\PasswordUpgraderRepository;
use App\Security\AccessDeniedHandler;
use App\Security\UserChecker;
use App\State\Role\RolePostProcessor;
use App\Tests\Api\JWTTestCase;
use PHPUnit\Framework\Attributes as PA;

/**
 * Tests the role POST.
 */
#[
    PA\CoversClass(RolePostController::class),
    PA\CoversClass(SendErrorController::class),
    PA\CoversClass(UnprocessableTranslationController::class),
    PA\UsesClass(AccessDeniedHandler::class),
    PA\UsesClass(ApiResponse::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleGetRepository::class),
    PA\UsesClass(LocaleNotFoundException::class),
    PA\UsesClass(PasswordUpgraderRepository::class),
    PA\UsesClass(Role::class),
    PA\UsesClass(RoleInput::class),
    PA\UsesClass(RoleTranslation::class),
    PA\UsesClass(RoleTranslationInput::class),
    PA\UsesClass(RolePostProcessor::class),
    PA\UsesClass(RolePostRepository::class),
    PA\UsesClass(ResourceLink::class),
    PA\UsesClass(User::class),
    PA\UsesClass(UserChecker::class),
    PA\UsesClass(Violation::class),
    PA\UsesClass(Violations::class),
    PA\Group('api'),
    PA\Group('api_roles'),
    PA\Group('api_roles_post'),
    PA\Group('role'),
    PA\TestDox('A role')
]
final class RolePostTest extends JWTTestCase
{
    // Methods :

    /**
     * Tests that a role
     * needs authentication to be posted.
     */
    public function testNeedsAuthenticationToBePosted(): void
    {
        $client = static::createClient();

        $client->request('POST', '/roles');

        self::assertResponseStatusCodeSame(401);
        self::assertSame(
            '{"code":401,"message":"JWT Token not found"}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Tests that a role
     * can not be posted
     * without ROLE_POST_ROLE.
     */
    public function testCanNotBePostedWithoutRolePostRole(): void
    {
        $client = static::createClient();

        $this->addAUserWithoutRole();
        $this->authenticateClient($client);

        $client->request('POST', '/roles');

        self::assertResponseStatusCodeSame(403);
        self::assertSame('', $client->getResponse()->getContent());
    }


    /**
     * Adds a user with ROLE_POST_ROLE.
     */
    private function addAUserWithRolePostRole(): void
    {
        $this->addAUserWithRole('ROLE_POST_ROLE');
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
     * Returns roles.
     * @return mixed[] roles.
     */
    public static function getRole(): array
    {
        $translationEN = [
            'description' => 'A new role.',
            'localeId' => 1
        ];

        $translationFR = [
            'description' => 'Un nouveau rôle.',
            'localeId' => 2
        ];

        $roleWithoutTranslation = [
            'name' => 'ROLE_NEW_ROLE'
        ];

        $roleWithATranslation = $roleWithoutTranslation;
        $roleWithATranslation['translations'] = [$translationEN];

        $roleWithManyTranslations = $roleWithATranslation;
        $roleWithATranslation['translations'][] = $translationFR;

        return [
            'no translation' => ['role' => $roleWithoutTranslation],
            'a translation' => ['role' => $roleWithATranslation],
            'many translations' => ['role' => $roleWithManyTranslations]
        ];
    }

    /**
     * Tests that a role can be created.
     * @param mixed[] $role the role.
     */
    #[
        PA\DataProvider('getRole'),
        PA\TestDox('Can be posted with $_dataName')
    ]
    public function testCanBePosted(array $role): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $this->addAnENLocale();
        $this->addAFRLocale();

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(201, 'POST to "/roles" failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame('/roles/1', $jsonResponse->link);
    }


    /**
     * Tests that a role can not be created
     * from invalid json.
     */
    public function testCanNotBePostedWithInvalidJson(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $client->request('POST', '/roles', content: 'test:');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid json.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame(
            'Invalid json.',
            $jsonResponse->message,
            'The error message is empty.'
        );
    }


    /**
     * Tests that an empty body request
     * can not create a role.
     */
    public function testCanNotBePostedWithAnEmptyBodyRequest(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $client->jsonRequest('POST', '/roles');
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed with an empty body request.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame(
            'Properties are missing.',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Adds a ROLE_NEW_ROLE role.
     */
    private function addARoleNewRole(): void
    {
        $container = self::getContainer();

        $role = new Role(
            'ROLE_NEW_ROLE'
        );

        $manager = $container->get('doctrine')->getManager();

        $manager->persist($role);
        $manager->flush();
    }

    /**
     * Tests that a role can not be created
     * when the name already exist.
     */
    public function testCanNotBePostedWhenTheNameAlrearyExist(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $this->addARoleNewRole();
        $role = [
            'name' => 'ROLE_NEW_ROLE'
        ];

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(409, 'POST to "/roles" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('name', $jsonResponse[0]->property);
        self::assertSame(
            'The name already exist.',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Tests that a role can not be created
     * when the name is blank.
     */
    public function testCanNotBePostedWhenTheNameIsBlank(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $role = [
            'name' => ''
        ];

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST to "/roles" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('name', $jsonResponse[0]->property);
        self::assertSame(
            'The name is blank.',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }

    /**
     * Tests that a role can not be created
     * when the name is too long.
     */
    public function testCanNotBePostedWhenTheNameIsTooLong(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $role = [
            'name' => 'ROLE_NEW_TOO_LONG_ROLEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE'
        ];

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST to "/roles" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('name', $jsonResponse[0]->property);
        self::assertSame(
            'The name is too long.',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Return invalid type names.
     * @return array invalid type names.
     */
    public static function getInvalidTypeNames(): array
    {
        return [
            'null' => [null],
            'an integer (5)' => [5],
            'an array ([])' => [[]]
        ];
    }

    /**
     * Tests that an invalid type name
     * can not be created.
     * @param mixed $invalidTypeName an invalid type name.
     */
    #[
        PA\DataProvider('getInvalidTypeNames'),
        PA\TestDox('Can not be posted when the name is $_dataName')
    ]
    public function testCanNotBePostedWithAnInvalidTypeName(mixed $invalidTypeName): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();

        $role = [
            'name' => $invalidTypeName
        ];

        $this->authenticateClient($client);

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400, 'POST did not failed for invalid name.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame(
            'Type error.',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Tests that a role can not be created
     * when the translation has a blank description.
     */
    public function testCanNotBePostedWhenTheTranslationHasABlankDescription(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);
        $this->addAnENLocale();

        $role = [
            'name' => 'ROLE_NEW_ROLE',
            'translations' => [
                [
                    'description' => '',
                    'localeId' => 1
                ]
            ]
        ];

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST to "/roles" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertCount(1, $jsonResponse, 'There must be one violation.');
        self::assertArrayHasKey(0, $jsonResponse);
        self::assertSame('translations[0].description', $jsonResponse[0]->property);
        self::assertSame(
            'The description is blank.',
            $jsonResponse[0]->message,
            'The violation message is empty.'
        );
    }


    /**
     * Tests that a role can not be created
     * when the translation has no locale.
     */
    public function testCanNotBePostedWhenTheTranslationHasNoLocale(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $role = [
            'name' => 'ROLE_NEW_ROLE',
            'translations' => [
                [
                    'description' => 'description'
                ]
            ]
        ];

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(400);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame(
            'Properties are missing.',
            $jsonResponse->message,
            'There is no error message.'
        );
    }


    /**
     * Tests that a role can not be created
     * when the translation locale does not exist.
     */
    public function testCanNotBePostedWhenTheTranslationLocaleDoesNotExist(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);

        $role = [
            'name' => 'ROLE_NEW_ROLE',
            'translations' => [
                [
                    'description' => 'description',
                    'localeId' => 1
                ]
            ]
        ];

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422);
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame(
            'The locale "1" does not exist.',
            $jsonResponse->message
        );
    }


    /**
     * Tests that a role can not be created
     * when a translation is sent twice.
     */
    public function testCanNotBePostedWhenATranslationIsSentTwice(): void
    {
        $server = [
            'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
        ];
        $client = static::createClient(server: $server);

        $this->addAUserWithRolePostRole();
        $this->authenticateClient($client);
        $this->addAnENLocale();

        $role = [
            'name' => 'ROLE_NEW_ROLE',
            'translations' => [
                [
                    'description' => 'a-description',
                    'localeId' => 1
                ],
                [
                    'description' => 'a-description',
                    'localeId' => 1
                ]
            ]
        ];

        $client->jsonRequest('POST', '/roles', $role);
        $apiResponse = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(422, 'POST to "/roles" did not failed.');
        self::assertJson($apiResponse);

        $jsonResponse = json_decode($apiResponse, false);

        self::assertSame(
            'The translation already exist.',
            $jsonResponse->message
        );
    }
}
