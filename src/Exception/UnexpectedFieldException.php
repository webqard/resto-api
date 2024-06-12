<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * An exception to throw
 * when a field is unexpected.
 */
class UnexpectedFieldException extends \UnexpectedValueException
{
    // Properties :

    /**
     * @var string the given field.
     */
    private string $givenField;

    /**
     * @var string[] the available fields.
     */
    private array $availableFields;


    // Magic methods :

    /**
     * The constructor.
     * @param string $givenField the given field.
     * @param string[] $availableFields the available fields.
     */
    public function __construct(
        string $message,
        string $givenField,
        array $availableFields,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->givenField = $givenField;
        $this->availableFields = $availableFields;
    }


    // Methods :

    /**
     * Returns the given field.
     * @return string the given field.
     */
    public function getGivenField(): string
    {
        return $this->givenField;
    }

    /**
     * Returns the available fields as a coma separated list.
     * @return string the available fields as a coma separated list.
     */
    public function getAvailableFields(): string
    {
        return implode(', ', $this->availableFields);
    }
}
