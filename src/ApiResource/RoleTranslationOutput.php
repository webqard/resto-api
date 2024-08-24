<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The role translation input.
 */
#[
    OA\Schema(
        type: "object",
        schema: "RoleTranslationOutput"
    )
]
class RoleTranslationOutput implements \JsonSerializable
{
    // Properties :

    /**
     * @var int the identifier/primary key.
     */
    #[OA\Property(example : 1)]
    private readonly int $id;

    /**
     * @var string|null the description.
     */
    #[OA\Property(example: "A role description.")]
    private readonly ?string $description;

    /**
     * @var int the locale identifier.
     */
    #[OA\Property(example: 1)]
    private readonly int $localeId;


    // Magic methods :

    /**
     * The constructor.
     * @param int $id the identifier/primary key.
     * @param string|null $description the description.
     * @param int $localeId the locale identifier.
     */
    public function __construct(
        int $id,
        ?string $description,
        int $localeId
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->localeId = $localeId;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'localeId' => $this->localeId
        ];
    }
}
