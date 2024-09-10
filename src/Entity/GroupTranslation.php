<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Association\LocaleAssociation;
use CyrilVerloop\DoctrineEntities\NullableDescription;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Name;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The group translation entity.
 */
#[
    ORM\Entity(),
    ORM\AttributeOverrides([
        new ORM\AttributeOverride(
            'name',
            new ORM\Column(
                nullable: false
            )
        )
    ]),
    UniqueEntity(
        fields: ["group", "locale"],
        message: "translation.alreadyExist"
    ),
    UniqueEntity(
        fields: ["name", "locale"],
        message: "name.alreadyExist"
    ),
    ORM\UniqueConstraint(columns: ["group_id", "locale_id"]),
    ORM\UniqueConstraint(columns: ["name", "locale_id"])
]
class GroupTranslation
{
    // Traits :
    use IntId;
    use NullableDescription;
    use LocaleAssociation;
    use Name;


    // Properties :

    /**
     * @var \App\Entity\Group the group.
     */
    #[
        ORM\JoinColumn(
            nullable: false,
            onDelete: "cascade"
        ),
        ORM\ManyToOne(targetEntity: Group::class)
    ]
    private Group $group;


    // Magic methods :

    /**
     * The constructor.
     * @param \App\Entity\Group $group the group.
     * @param \App\Entity\Locale $locale the locale.
     * @param string $name the name.
     * @param string|null $description the description.
     */
    public function __construct(
        Group $group,
        Locale $locale,
        string $name,
        ?string $description = null
    ) {
        $this->id = null;
        $this->group = $group;
        $this->locale = $locale;
        $this->name = $name;
        $this->description = $description;
    }
}
