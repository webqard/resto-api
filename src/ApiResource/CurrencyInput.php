<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The currency input.
 */
#[
    OA\Schema(
        type: "object",
        schema: "CurrencyInput"
    )
]
class CurrencyInput
{
    // Properties :

    /**
     * @var string the code.
     */
    #[
        Assert\Currency(message: "code.invalid"),
        Assert\NotBlank(message: "code.blankError"),
        OA\Property(example : "EUR")
    ]
    private string $code;

    /**
     * @var int the number of decimals.
     */
    #[
        Assert\PositiveOrZero(message: "decimals.negativeError"),
    ]
    private int $decimals;


    // Magic methods :

    /**
     * The constructor.
     * @param int $decimals the number of decimals.
     */
    public function __construct(string $code, int $decimals)
    {
        $this->code = $code;
        $this->decimals = $decimals;
    }


    // Accessors :

    /**
     * Returns the code.
     * @return string the code.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Returns the number of decimals.
     * @return int the number of decimals.
     */
    public function getDecimals(): int
    {
        return $this->decimals;
    }
}
