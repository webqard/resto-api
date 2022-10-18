<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The response.
 */
#[
    OA\Schema(
        type: "object",
        schema: "ApiResponse"
    )
]
class ApiResponse implements \JsonSerializable
{
    // Properties :

    /**
     * @var string the message.
     */
    #[OA\Property(example : "Here is some information.")]
    private string $message;


    // Magic methods :

    /**
     * The constructor.
     * @param string $message the message.
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'message' => $this->message
        ];
    }
}
