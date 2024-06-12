<?php

declare(strict_types=1);

namespace App\Tests\Exception;

use App\Exception\UnexpectedFieldException;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests UnexpectedFieldException.
 */
#[
    PA\CoversClass(UnexpectedFieldException::class),
    PA\Group('exceptions'),
    PA\Group('exceptions_unexpectedFieldException')
]
final class UnexpectedFieldExceptionTest extends TestCase
{
    // Methods :

    /**
     * Test that the default code is 0.
     */
    public function testCanHave0AsDefaultCode(): void
    {
        $exception = new UnexpectedFieldException(
            'message',
            'given-field',
            []
        );

        self::assertSame(0, $exception->getCode());
    }


    /**
     * Test that the message can be given.
     */
    public function testCanGetTheMessage(): void
    {
        $exception = new UnexpectedFieldException(
            'message',
            'given-field',
            []
        );

        self::assertSame('message', $exception->getMessage());
    }


    /**
     * Test that the given field can be given.
     */
    public function testCanGetTheGivenField(): void
    {
        $exception = new UnexpectedFieldException(
            'message',
            'given-field',
            [
                'available-field',
                'available-field2'
            ]
        );

        self::assertSame('given-field', $exception->getGivenField());
    }


    /**
     * Test that the available fields can be given.
     */
    public function testCanGetTheAvailableFieldsAsACommaSeparatedString(): void
    {
        $exception = new UnexpectedFieldException(
            'message',
            'given-field',
            [
                'available-field',
                'available-field2'
            ]
        );

        self::assertSame(
            'available-field, available-field2',
            $exception->getAvailableFields()
        );
    }

    /**
     * Test that an empty string is givent
     * when there are no available field.
     */
    public function testCanGetAnEmptyStringWhenThereAreNoAvailableField(): void
    {
        $exception = new UnexpectedFieldException(
            'message',
            'given-field',
            []
        );

        self::assertSame(
            '',
            $exception->getAvailableFields()
        );
    }
}
