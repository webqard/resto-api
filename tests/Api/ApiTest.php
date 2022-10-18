<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the API documentation.
 *
 * @coversDefaultClass \App\Controller\ApiController
 * @covers ::api
 * @group api
 * @group api_doc
 */
final class ApiTest extends WebTestCase
{
    // Methods :

    /**
     * Tests that the API.
     */
    public function testCanRedirectToApiDocumentation(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        self::assertResponseStatusCodeSame(301, 'GET to "/index.html" failed.');
    }
}
