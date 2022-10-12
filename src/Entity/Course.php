<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\CategoryAssociation;
use App\Entity\Property\SetMenuOnly;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The course entity.
 */
#[ORM\Entity()]
class Course extends Product
{
    // Traits :
    use CategoryAssociation;
    use SetMenuOnly;


    // Properties :

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\OneToMany(
            targetEntity: CourseTranslation::class,
            mappedBy: 'course',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;

    /**
     * @var \Doctrine\Common\Collections\Collection the prices.
     */
    #[
        ORM\OneToMany(
            targetEntity: CoursePrice::class,
            mappedBy: 'course',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $prices;

    /**
     * @var \Doctrine\Common\Collections\Collection the pictures.
     */
    #[
        ORM\OneToMany(
            targetEntity: CoursePicture::class,
            mappedBy: 'course',
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $pictures;


    // Magic methods :

    /**
     * @param bool $available the availability.
     * @param int $priority the priority.
     * @param bool $setMenuOnly if the course is available only in set menu.
     * @param \App\Entity\Category|null $category the category.
     */
    public function __construct(
        bool $available = true,
        int $priority = 0,
        bool $setMenuOnly = false,
        ?Category $category = null
    ) {
        parent::__construct(
            $available,
            $priority
        );

        $this->setMenuOnly = $setMenuOnly;
        $this->category = $category;
        $this->translations = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }
}
