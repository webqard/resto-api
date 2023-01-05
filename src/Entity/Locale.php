<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Property\Code;
use CyrilVerloop\DoctrineEntities\IntId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The locale entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["code"]),
    UniqueEntity(
        fields: ["code"],
        message: "code.alreadyExist"
    )
]
class Locale extends IntId
{
    //Traits :
    use Code;


    // Properties :

    #[
        ORM\Column(
            length: 7,
            unique: true
        )
    ]
    private string $code;


    // Magic methods :

    /**
     * @param string $code the code.
     */
    public function __construct(string $code)
    {
        parent::__construct();

        $this->code = $code;
    }
}
