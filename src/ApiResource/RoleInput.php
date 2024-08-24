<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The role input.
 */
#[
    OA\Schema(
        type: "object",
        schema: "RoleInput"
    )
]
class RoleInput
{
    // Properties :

    /**
     * @var string the name.
     */
    #[
        Assert\NotBlank(message: "name.blankError"),
        Assert\Length(max: 100, maxMessage: "name.tooLong"),
        OA\Property(example: "ROLE_USER")
    ]
    private readonly string $name;

    /**
     * @var \App\ApiResource\RoleTranslationInput[]|null the translations.
     */
    #[
        Assert\Valid(),
        OA\Property(
            type: 'array',
            items: new OA\Items(RoleTranslationInput::class)
        )
    ]
    private readonly ?array $translations;


    // Magic methods :

    /**
     * The constructor.
     * @param string $name the name.
     * @param \App\ApiResource\RoleTranslationInput[]|null $translations the translations.
     */
    public function __construct(
        string $name,
        ?array $translations = null
    ) {
        $this->name = $name;
        $this->translations = $translations;
    }


    // Accessors :

    /**
     * Returns the name.
     * @return string the name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the translations.
     * @return \App\ApiResource\RoleTranslationInput[]|null the translations.
     */
    public function getTranslations(): ?array
    {
        return $this->translations;
    }
}
