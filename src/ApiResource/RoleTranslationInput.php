<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The role translation input.
 */
#[
    OA\Schema(
        type: "object",
        schema: "RoleTranslationInput"
    )
]
class RoleTranslationInput
{
    // Properties :

    /**
     * @var string the description.
     */
    #[
        Assert\NotBlank(message: "description.blankError"),
        OA\Property(example: "A role description.")
    ]
    private readonly string $description;

    /**
     * @var int the locale identifier.
     */
    #[
        Assert\NotBlank(message: "locale.blankError"),
        OA\Property(example: 1)
    ]
    private readonly int $localeId;


    // Magic methods :

    /**
     * The constructor.
     * @param string $description the description.
     * @param int $localeId the locale identifier.
     */
    public function __construct(
        string $description,
        int $localeId
    ) {
        $this->description = $description;
        $this->localeId = $localeId;
    }


    // Accessors :

    /**
     * Returns the description.
     * @return string the description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns the locale identifier.
     * @return int the locale identifier.
     */
    public function getLocaleId(): int
    {
        return $this->localeId;
    }
}
