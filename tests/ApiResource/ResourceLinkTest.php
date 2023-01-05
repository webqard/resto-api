<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\ResourceLink;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API resource link.
 *
 * @coversDefaultClass \App\ApiResource\ResourceLink
 * @covers ::__construct
 * @covers ::jsonSerialize
 * @group apiResource
 * @group apiResource_resourceLink
 */
final class ResourceLinkTest extends TestCase
{
    // Methods :

    /**
     * Tests that the resource link can be serialised.
     */
    public function testCanSerialiseResourceLink(): void
    {
        $resourceLink = new ResourceLink('/entities/1');

        $unserialisedResourceLink = json_decode(json_encode($resourceLink));

        self::assertObjectHasAttribute('link', $unserialisedResourceLink);
        self::assertSame('/entities/1', $unserialisedResourceLink->link);
    }
}
