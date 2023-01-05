<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The violations.
 */
class Violations implements \JsonSerializable
{
    // Properties :

    /**
     * @var array the violations.
     */
    private array $violations;


    // Magic methods :

    /**
     * The constructor.
     * @param array $violations the violations.
     */
    public function __construct(array $violations = [])
    {
        $this->violations = $violations;
    }


    // Methods :

    /**
     * Adds a violation to the list.
     * @param \App\ApiResource\Violation $violation
     */
    public function add(Violation $violation): void
    {
        $this->violations[] = $violation;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return $this->violations;
    }
}
