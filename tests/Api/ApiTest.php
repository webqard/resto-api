<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Controller\ApiController;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the API documentation.
 */
#[
    PA\CoversClass(ApiController::class),
    PA\Group('api'),
    PA\Group('api_doc')
]
final class ApiTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that the root of the API
     * is redirecting to the API documentation.
     */
    public function testCanRedirectToApiDocumentation(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        self::assertResponseStatusCodeSame(301, 'GET to "/index.html" failed.');
    }
}
