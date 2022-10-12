<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The category's translation entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["category_id", "locale_id"])
]
class CategoryTranslation extends ProductTranslation
{
    // Properties :

    /**
     * @var \App\Entity\Category the category.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(
            targetEntity: Category::class,
            inversedBy: "translations"
        )
    ]
    private Category $category;


    // Magic methods :

    /**
     * @param \App\Entity\Category $category the category.
     * @param string $name the name.
     * @param string $slug the slug.
     * @param \App\Entity\Locale $locale the locale.
     * @param string|null $description the description.
     */
    public function __construct(
        Category $category,
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

        $this->category = $category;
    }
}
