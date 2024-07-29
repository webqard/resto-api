<?php

declare(strict_types=1);

namespace App\Entity;

use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Name;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The role entity.
 */
#[
    ORM\Entity(),
    UniqueEntity(
        fields: ["name"],
        message: "name.alreadyExist"
    ),
    ORM\AttributeOverrides([
        new ORM\AttributeOverride(
            'name',
            new ORM\Column(
                length: 100,
                unique: true
            )
        )
    ])
]
class Role
{
    // Traits :
    use IntId;
    use Name;


    // Properties :

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\JoinColumn(onDelete: "CASCADE"),
        ORM\OneToMany(
            targetEntity: RoleTranslation::class,
            mappedBy: 'role',
            cascade: ["persist"],
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;


    // Magic methods :

    /**
     * The constructor.
     * @param string $name the name.
     */
    public function __construct(string $name)
    {
        $this->id = null;
        $this->name = $name;
        $this->translations = new ArrayCollection();
    }


    // Accessors :

    /**
     * Returns the translations.
     * @return \Doctrine\Common\Collections\Collection the translations.
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }


    // Collections :

    /**
     * Adds a translation.
     * @param \App\Entity\RoleTranslation $translation a translation.
     */
    public function addTranslation(RoleTranslation $translation): void
    {
        if ($this->translations->contains($translation) === false) {
            $this->translations->add($translation);
        }
    }

    /**
     * Removes a translation.
     * @param \App\Entity\RoleTranslation $translation a translation.
     */
    public function removeTranslation(RoleTranslation $translation): void
    {
        $this->translations->removeElement($translation);
    }
}
