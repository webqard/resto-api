<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\AuthenticationFailureHandler;
use PHPUnit\Framework\Attributes as PA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Tests the AuthenticationFailureHandler.
 */
#[
    PA\CoversClass(AuthenticationFailureHandler::class),
    PA\Group('security'),
    PA\Group('security_authenticationFailure'),
]
final class AuthenticationFailureHandlerTest extends KernelTestCase
{
    // Methods :

    /**
     * Test that the AuthenticationFailureHandler
     * gives with an empty 401 response.
     */
    public function testGivesAnEmptyUnauthorizedResponse(): void
    {
        $request = new Request();
        $authenticationException = new AuthenticationException();
        $authenticationFailureHandler = new AuthenticationFailureHandler();

        $response = $authenticationFailureHandler->onAuthenticationFailure($request, $authenticationException);

        self::assertSame(401, $response->getStatusCode());
        self::assertSame('', $response->getContent());
    }
}
