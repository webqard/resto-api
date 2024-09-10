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
     * @var string the description.
     */
    #[OA\Property(example: "A role to do something.")]
    private readonly string $description;

    /**
     * @var \App\ApiResource\LocaleOutput the locale.
     */
    #[OA\Property()]
    private readonly LocaleOutput $locale;


    // Magic methods :

    /**
     * The constructor.
     * @param int $id the identifier/primary key.
     * @param string $description the description.
     * @param \App\ApiResource\LocaleOutput $locale the locale.
     */
    public function __construct(
        int $id,
        string $description,
        LocaleOutput $locale
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->locale = $locale;
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
            'locale' => $this->locale
        ];
    }
}
