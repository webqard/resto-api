<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\LocaleAssociation;
use CyrilVerloop\DoctrineEntities\Description;
use CyrilVerloop\DoctrineEntities\IntId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The role translation entity.
 */
#[
    ORM\Entity(),
    ORM\AttributeOverrides([
        new ORM\AttributeOverride(
            'description',
            new ORM\Column(
                nullable: false
            )
        )
    ]),
    UniqueEntity(
        fields: ["role", "locale"],
        message: "translation.alreadyExist"
    ),
    ORM\UniqueConstraint(columns: ["role_id", "locale_id"])
]
class RoleTranslation
{
    // Traits :
    use IntId;
    use Description;
    use LocaleAssociation;


    // Properties :

    /**
     * @var \App\Entity\Role the role.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: Role::class)
    ]
    private Role $role;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Entity\Role $role the role.
     * @param string $description the description.
     * @param \App\Entity\Locale $locale the locale.
     */
    public function __construct(
        Role $role,
        string $description,
        Locale $locale
    ) {
        $this->id = null;
        $this->role = $role;
        $this->description = $description;
        $this->locale = $locale;
    }
}
