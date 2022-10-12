<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\DrinkAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The drink's price entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["drink_id", "currency_id"])
]
class DrinkPrice extends ProductPrice
{
    // Traits :
    use DrinkAssociation;


    // Properties :

    /**
     * @var int the quantity in millilitre.
     */
    #[ORM\Column()]
    private int $quantity;


    // Magic methods :

    /**
     * @param \App\Entity\Drink $drink the drink.
     * @param int $value the value in the smallest unit.
     * @param int $quantity the quantity in millilitre.
     * @param \App\Entity\Currency $currency the currency.
     * @param \DateTimeImmutable $beginDate the begin date.
     * @param \DateTimeImmutable|null $endDate the end date.
     */
    public function __construct(
        Drink $drink,
        int $value,
        int $quantity,
        Currency $currency,
        \DateTimeImmutable $beginDate,
        ?\DateTimeImmutable $endDate = null
    ) {
        parent::__construct(
            $value,
            $currency,
            $beginDate,
            $endDate
        );

        $this->quantity = $quantity;
        $this->drink = $drink;
    }
}
