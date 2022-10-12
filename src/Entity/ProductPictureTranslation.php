<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\LocaleAssociation;
use CyrilVerloop\DoctrineEntities\IntId;
use Doctrine\ORM\Mapping as ORM;

/**
 * A base entity for
 * product picture's translation.
 */
#[ORM\MappedSuperclass()]
abstract class ProductPictureTranslation extends IntId
{
    // Traits :
    use LocaleAssociation;


    // Properties :

    /**
     * @var string|null the alternative of the picture (ex : \<img alt="" />).
     */
    #[ORM\Column(nullable: true)]
    protected ?string $alternative;

    /**
     * @var string|null the title of the picture (ex : \<img title="" />).
     */
    #[
        ORM\Column(
            length: 100,
            nullable: true
        )
    ]
    protected ?string $title;


    // Magic methods :

    /**
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $alternative the alternative.
     * @param string|null $title the title.
     */
    public function __construct(
        Locale $locale,
        ?string $alternative = null,
        ?string $title = null
    ) {
        parent::__construct();

        $this->locale = $locale;
        $this->alternative = $alternative;
        $this->title = $title;
    }
}
