<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The locale input.
 */
#[
    OA\Schema(
        type: "object",
        schema: "LocaleInput"
    )
]
class LocaleInput
{
    // Properties :

    /**
     * @var string the code.
     */
    #[
        Assert\Locale(
            canonicalize: true,
            message: "code.invalid"
        ),
        Assert\NotBlank(message: "code.blankError"),
        OA\Property(example : "en_GB")
    ]
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


    // Accessors :

    /**
     * Returns the code.
     * @return string the code.
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
