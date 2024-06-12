<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The locale output.
 */
#[
    OA\Schema(
        type: "object",
        schema: "LocaleOutput"
    )
]
class LocaleOutput implements \JsonSerializable
{
    // Properties :

    /**
     * @var int the identifier/primary key.
     */
    #[OA\Property(example : 1)]
    private int $id;

    /**
     * @var string the code.
     */
    #[OA\Property(example : "en_GB")]
    private string $code;


    // Magic methods :

    /**
     * The constructor.
     * @param int $id the identifier/primary key.
     * @param string $code the code.
     */
    public function __construct(int $id, string $code)
    {
        $this->id = $id;
        $this->code = $code;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'code' => $this->code
        ];
    }
}
