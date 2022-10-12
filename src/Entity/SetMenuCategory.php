<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\SetMenuAssociation;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Priority;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The set menu's category entity.
 */
#[
    ORM\Entity(),
    ORM\UniqueConstraint(columns: ["set_menu_id", "category_id"])
]
class SetMenuCategory extends IntId
{
    // Traits :
    use Priority;
    use SetMenuAssociation;


    // Properties :

    /**
     * @var \App\Entity\Category the category.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: Category::class)
    ]
    private Category $category;

    /**
     * @var \App\Entity\SetMenuCategory|null the parent set menu category.
     */
    #[
        ORM\JoinColumn(onDelete: "SET NULL"),
        ORM\ManyToOne(
            targetEntity: SetMenuCategory::class,
            inversedBy: "children",
            fetch: "EXTRA_LAZY"
        )
    ]
    private ?SetMenuCategory $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection the children set menu categories.
     */
    #[
        ORM\OneToMany(
            targetEntity: SetMenuCategory::class,
            mappedBy: 'parent',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $children;


    // Magic methods :

    /**
     * @param \App\Entity\SetMenu $setMenu the set menu.
     * @param \App\Entity\Category $category the category.
     * @param int $priority the priority.
     * @param \App\Entity\SetMenuCategory|null $parent the parent set menu category.
     */
    public function __construct(
        SetMenu $setMenu,
        Category $category,
        int $priority = 0,
        ?SetMenuCategory $parent = null
    ) {
        parent::__construct();

        $this->setMenu = $setMenu;
        $this->category = $category;
        $this->priority = $priority;
        $this->parent = $parent;
        $this->children = new ArrayCollection();
    }
}
