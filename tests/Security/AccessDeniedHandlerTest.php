<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\AccessDeniedHandler;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests the AccessDeniedHandler.
 */
#[
    PA\CoversClass(AccessDeniedHandler::class),
    PA\Group('security'),
    PA\Group('security_accessDeniedHandler'),
]
final class AccessDeniedHandlerTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the AccessDeniedHandler
     * gives with an empty 403 response.
     */
    public function testGivesAnEmptyForbiddenResponse(): void
    {
        $request = new Request();
        $accessDeniedException = new AccessDeniedException();
        $accessDeniedHandler = new AccessDeniedHandler();

        $response = $accessDeniedHandler->handle($request, $accessDeniedException);

        self::assertSame(403, $response->getStatusCode());
        self::assertSame('', $response->getContent());
    }
}
