<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\SetMenuAssociation;
use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu's translation entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["set_menu_id", "locale_id"]),
]
class SetMenuTranslation extends ProductTranslation
{
    // Traits :
    use SetMenuAssociation;


    // Magic methods :

    /**
     * @param \App\Entity\SetMenu $setMenu the set menu.
     * @param string $name the name.
     * @param string $slug the slug.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $description the description.
     */
    public function __construct(
        SetMenu $setMenu,
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

        $this->setMenu = $setMenu;
    }
}
