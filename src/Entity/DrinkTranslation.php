<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\DrinkAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The drink's translation entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["drink_id", "locale_id"])
]
class DrinkTranslation extends ProductTranslation
{
    // Traits :
    use DrinkAssociation;


    // Magic methods :

    /**
     * @param \App\Entity\Drink $drink the drink.
     * @param string $name the name.
     * @param string $slug the slug.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $description the description.
     */
    public function __construct(
        Drink $drink,
        string $name,
        string $slug,
        Locale $locale,
        ?string $description = null
    ) {
        parent::__construct(
            $name,
            $slug,
            $locale,
            $description
        );

        $this->drink = $drink;
    }
}
