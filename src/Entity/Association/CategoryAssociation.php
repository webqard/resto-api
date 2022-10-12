<?php

declare(strict_types=1);

namespace App\Entity\Association;

use App\Entity\Category;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait that adds a 'Category' field to an entity.
 */
trait CategoryAssociation
{
    // Properties :

    /**
     * @var \App\Entity\Category|null the category.
     */
    #[
        ORM\JoinColumn(onDelete: "SET NULL"),
        ORM\ManyToOne(targetEntity: Category::class)
    ]
    private ?Category $category;
}
