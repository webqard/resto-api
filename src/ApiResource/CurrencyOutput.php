<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The currency.
 */
#[
    OA\Schema(
        type: "object",
        schema: "CurrencyOutput"
    )
]
class CurrencyOutput implements \JsonSerializable
{
    // Properties :

    /**
     * @var string the code.
     */
    #[OA\Property(example : "en_GB")]
    private string $code;

    /**
     * @var int the number of decimals.
     */
    #[OA\Property(example : 2)]
    private int $decimals;


    // Magic methods :

    /**
     * The constructor.
     * @param string $code the code.
     * @param int $decimals the number of decimals.
     */
    public function __construct(string $code, int $decimals)
    {
        $this->code = $code;
        $this->decimals = $decimals;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'code' => $this->code,
            'decimals' => $this->decimals
        ];
    }
}
