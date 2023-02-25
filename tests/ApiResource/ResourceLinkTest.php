<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\ResourceLink;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API resource link.
 */
#[
    PA\CoversClass(ResourceLink::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_resourceLink')
]
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

        self::assertSame('/entities/1', $unserialisedResourceLink->link);
    }
}
