<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Property\Code;
use CyrilVerloop\DoctrineEntities\IntId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The currency entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["code"]),
    UniqueEntity(
        fields: ["code"],
        message: "code.alreadyExist"
    ),
    ORM\AttributeOverrides([
        new ORM\AttributeOverride(
            'code',
            new ORM\Column(
                length: 3,
                unique: true
            )
        )
    ])
]
class Currency
{
    //Traits :
    use IntId;
    use Code;


    // Properties :

    /**
     * @var int the number of decimals.
     */
    #[
        ORM\Column(
            options: ["unsigned" => true]
        )
    ]
    private int $decimals;


    // Magic methods :

    /**
     * The constructor.
     * @param string $code the code.
     * @param int $decimals the number of decimals.
     */
    public function __construct(string $code, int $decimals)
    {
        $this->id = null;
        $this->code = $code;
        $this->decimals = $decimals;
    }


    // Accessors :

    /**
     * Returns the number of decimals.
     * @return int the number of decimals.
     */
    public function getDecimals(): int
    {
        return $this->decimals;
    }


    // Mutators :

    /**
     * Changes the number of decimals.
     * @param int $decimals the number of decimals.
     */
    public function setDecimals(int $decimals): void
    {
        $this->decimals = $decimals;
    }
}
