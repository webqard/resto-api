<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\LocaleAssociation;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\NullableDescription;
use CyrilVerloop\DoctrineEntities\Slug;
use Doctrine\ORM\Mapping as ORM;

/**
 * A base entity for
 * product's translation.
 */
#[ORM\MappedSuperclass()]
abstract class ProductTranslation
{
    // Traits :
    use IntId;
    use LocaleAssociation;
    use Slug;
    use NullableDescription;


    // Properties :

    /**
     * @var string the name.
     */
    #[ORM\Column(length: 100)]
    protected string $name;


    // Magic methods :

    /**
     * The constructor.
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
        $this->id = null;
        $this->name = $name;
        $this->slug = $slug;
        $this->locale = $locale;
        $this->description = $description;
    }
}
