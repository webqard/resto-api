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
    ),
    ORM\AttributeOverrides([
        new ORM\AttributeOverride(
            'code',
            new ORM\Column(
                length: 7,
                unique: true
            )
        )
    ])
]
class Locale
{
    //Traits :
    use IntId;
    use Code;


    // Magic methods :

    /**
     * The constructor.
     * @param string $code the code.
     */
    public function __construct(string $code)
    {
        $this->id = null;
        $this->code = $code;
    }
}
