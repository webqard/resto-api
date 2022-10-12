<?php

declare(strict_types=1);

namespace App\Entity;

use CyrilVerloop\DoctrineEntities\IntId;
use Doctrine\ORM\Mapping as ORM;

/**
 * A base entity for
 * product's price.
 */
#[ORM\MappedSuperclass()]
abstract class ProductPrice extends IntId
{
    // Properties :

    /**
     * @var int the value in the smallest unit.
     */
    #[ORM\Column()]
    protected int $value;

    /**
     * @var \App\Entity\Currency the currency.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: Currency::class)
    ]
    protected Currency $currency;

    /**
     * @var \DateTimeImmutable the begin date.
     */
    #[ORM\Column()]
    protected \DateTimeImmutable $beginDate;

    /**
     * @var \DateTimeImmutable|null the end date.
     */
    #[
        ORM\Column(
            nullable: true,
            options: [
                "default" => null
            ]
        )
    ]
    protected ?\DateTimeImmutable $endDate;


    // Magic methods :

    /**
     * @param int $value the value in the smallest unit.
     * @param \App\Entity\Currency $currency the currency.
     * @param \DateTimeImmutable $beginDate the begin date.
     * @param \DateTimeImmutable|null $endDate the end date.
     */
    public function __construct(
        int $value,
        Currency $currency,
        \DateTimeImmutable $beginDate,
        ?\DateTimeImmutable $endDate = null
    ) {
        parent::__construct();

        $this->value = $value;
        $this->currency = $currency;
        $this->beginDate = $beginDate;
        $this->endDate = $endDate;
    }
}
