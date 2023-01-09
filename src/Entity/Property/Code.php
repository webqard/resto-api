<?php

declare(strict_types=1);

namespace App\Entity\Property;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait that adds a 'code' field to an entity.
 */
trait Code
{
    // Properties :

    /**
     * @var string the code.
     */
    #[ORM\Column(unique: true)]
    private string $code;


    // Accessors :

    /**
     * Returns the code.
     * @return string the code.
     */
    public function getCode(): string
    {
        return $this->code;
    }


    // Mutators :

    /**
     * Changes the code.
     * @param string $code the code.
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
