<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The role output.
 */
#[
    OA\Schema(
        type: "object",
        schema: "RoleOutput"
    )
]
class RoleOutput implements \JsonSerializable
{
    // Properties :

    /**
     * @var int the identifier/primary key.
     */
    #[OA\Property(example : 1)]
    private int $id;

    /**
     * @var string the name.
     */
    #[
        Assert\NotBlank(message: "name.blankError"),
        OA\Property(example: "ROLE_USER")
    ]
    private readonly string $name;

    /**
     * @var \App\ApiResource\RoleTranslationOutput[]|null the translations.
     */
    #[OA\Property(
        type: 'array',
        items: new OA\Items(RoleTranslationOutput::class)
    )]
    private readonly ?array $translations;


    // Magic methods :

    /**
     * The constructor.
     * @param int $id the identifier/primary key.
     * @param string $name the name.
     * @param \App\ApiResource\RoleTranslationOutput[]|null $translations the translations.
     */
    public function __construct(
        int $id,
        string $name,
        ?array $translations = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->translations = $translations;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'translations' => $this->translations
        ];
    }
}
