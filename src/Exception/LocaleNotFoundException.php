<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * An exception to throw
 * when a locale is not found.
 */
final class LocaleNotFoundException extends \OutOfBoundsException
{
    // Properties :

    /**
     * @var int the indentifier.
     */
    private int $indentifier;


    // Magic methods :

    /**
     * The constructor.
     * @param int $indentifier the indentifier.
     */
    public function __construct(
        string $message,
        int $indentifier,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->indentifier = $indentifier;
    }


    // Methods :

    /**
     * Returns the indentifier.
     * @return int indentifier.
     */
    public function getIndentifier(): int
    {
        return $this->indentifier;
    }
}
