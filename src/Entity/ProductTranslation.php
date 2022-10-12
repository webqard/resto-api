<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\LocaleAssociation;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Slug;
use Doctrine\ORM\Mapping as ORM;

/**
 * A base entity for
 * product's translation.
 */
#[ORM\MappedSuperclass()]
abstract class ProductTranslation extends IntId
{
    // Traits :
    use LocaleAssociation;
    use Slug;


    // Properties :

    /**
     * @var string the name.
     */
    #[ORM\Column(length: 100)]
    protected string $name;

    /**
     * @var string|null the description.
     */
    #[
        ORM\Column(
            nullable: true,
            options: [
                "default" => null
            ]
        )
    ]
    protected ?string $description;


    // Magic methods :

    /**
     * @param string $name the name.
     * @param string $slug the slug.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $description the description.
     */
    public function __construct(
        string $name,
        string $slug,
        Locale $locale,
        ?string $description = null
    ) {
        parent::__construct();

        $this->name = $name;
        $this->slug = $slug;
        $this->locale = $locale;
        $this->description = $description;
    }
}
