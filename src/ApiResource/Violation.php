<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The violation.
 */
#[
    OA\Schema(
        type: "object",
        schema: "Violation"
    )
]
class Violation implements \JsonSerializable
{
    // Properties :

    /**
     * @var string the property name.
     */
    #[OA\Property(example : 'name')]
    private string $property;

    /**
     * @var string the message.
     */
    #[OA\Property(example : 'Some error message.')]
    private string $message;


    // Magic methods :

    /**
     * The constructor.
     * @param string $property the property name.
     * @param string $message the message.
     */
    public function __construct(string $property, string $message)
    {
        $this->property = $property;
        $this->message = $message;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'property' => $this->property,
            'message' => $this->message
        ];
    }
}
