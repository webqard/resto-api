<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\DrinkAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu's drink entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["drink_id", "set_menu_id"])
]
class SetMenuDrink extends SetMenuProduct
{
    // Traits :
    use DrinkAssociation;


    // Magic methods :

    /**
     * @param \App\Entity\Drink $drink the drink.
     * @param \App\Entity\SetMenu $setMenu the set menu.
     * @param \App\Entity\SetMenuCategory|null $setMenuCategory the set menu category.
     * @param int $priority the priority.
     */
    public function __construct(
        Drink $drink,
        SetMenu $setMenu,
        ?SetMenuCategory $setMenuCategory = null,
        int $priority = 0
    ) {
        parent::__construct(
            $setMenu,
            $setMenuCategory,
            $priority
        );

        $this->drink = $drink;
    }
}
