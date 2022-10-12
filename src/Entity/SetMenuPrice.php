<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\SetMenuAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu's price entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["set_menu_id", "currency_id"])
]
class SetMenuPrice extends ProductPrice
{
    // Traits :
    use SetMenuAssociation;


    // Magic methods :

    /**
     * @param \App\Entity\SetMenu $setMenu the set menu.
     * @param int $value the value in the smallest unit.
     * @param \App\Entity\Currency $currency the currency.
     * @param \DateTimeImmutable $beginDate the begin date.
     * @param \DateTimeImmutable|null $endDate the end date.
     */
    public function __construct(
        SetMenu $setMenu,
        int $value,
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

        $this->setMenu = $setMenu;
    }
}
