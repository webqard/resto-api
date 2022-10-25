<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The locale.
 */
#[
    OA\Schema(
        type: "object",
        schema: "Locale"
    )
]
class LocaleOutput implements \JsonSerializable
{
    // Properties :

    /**
     * @var string the code.
     */
    #[OA\Property(example : "en_GB")]
    private string $code;


    // Magic methods :

    /**
     * The constructor.
     * @param string $code the code.
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'code' => $this->code
        ];
    }
}
