<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The link to a resource.
 */
#[
    OA\Schema(
        type: "object",
        schema: "ResourceLink"
    )
]
class ResourceLink implements \JsonSerializable
{
    // Properties :

    /**
     * @var string the link to a resource.
     */
    #[OA\Property(example : "/courses/1")]
    private string $link;


    // Magic methods :

    /**
     * The constructor.
     * @param string $link the link to a resource.
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }


    // JsonSerializable :

    /**
     * @return mixed the serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'link' => $this->link
        ];
    }
}
