<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Property\SetMenuOnly;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Priority;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The category entity.
 */
#[ORM\Entity()]
class Category extends IntId
{
    // Traits :
    use Priority;
    use SetMenuOnly;


    // Properties :

    /**
     * @var \App\Entity\Category|null the parent category.
     */
    #[
        ORM\JoinColumn(onDelete: "SET NULL"),
        ORM\ManyToOne(
            targetEntity: Category::class,
            inversedBy: "children",
            fetch: "EXTRA_LAZY"
        )
    ]
    private ?Category $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection the children categories.
     */
    #[
        ORM\OneToMany(
            targetEntity: Category::class,
            mappedBy: 'parent',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $children;

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\OneToMany(
            targetEntity: CategoryTranslation::class,
            mappedBy: 'category',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;


    // Magic methods :

    /**
     * @param int $priority the priority.
     * @param bool $setMenuOnly if the course is available only in set menu.
     * @param \App\Entity\Category|null $parent the parent category.
     */
    public function __construct(
        int $priority = 0,
        bool $setMenuOnly = false,
        ?Category $parent = null
    ) {
        parent::__construct();

        $this->priority = $priority;
        $this->setMenuOnly = $setMenuOnly;
        $this->parent = $parent;
        $this->children = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }
}
