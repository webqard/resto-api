<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\User;
use CyrilVerloop\DoctrineEntities\IntId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The group entity.
 */
#[
    ORM\Entity(),
    ORM\Table(name: '`group`')
]
class Group
{
    // Traits :
    use IntId;


    // Properties :

    /**
     * @var \Doctrine\Common\Collections\Collection the translations.
     */
    #[
        ORM\JoinColumn(onDelete: "CASCADE"),
        ORM\OneToMany(
            targetEntity: GroupTranslation::class,
            mappedBy: 'group',
            cascade: ["persist"],
            fetch: "EXTRA_LAZY"
        )
    ]
    private Collection $translations;

    /**
     * @var \Doctrine\Common\Collections\Collection the users.
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    private Collection $users;

    /**
     * @var \Doctrine\Common\Collections\Collection the roles.
     */
    #[ORM\ManyToMany(targetEntity: Role::class)]
    private Collection $roles;


    // Magic methods :

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->id = null;
        $this->translations = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
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

    /**
     * Returns the users.
     * @return \Doctrine\Common\Collections\Collection the users.
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Returns the roles.
     * @return \Doctrine\Common\Collections\Collection the roles.
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }


    // Collections :

    /**
     * Adds a translation.
     * @param \App\Entity\GroupTranslation $translation a translation.
     */
    public function addTranslation(GroupTranslation $translation): void
    {
        if ($this->translations->contains($translation) === false) {
            $this->translations->add($translation);
        }
    }

    /**
     * Removes a translation.
     * @param \App\Entity\GroupTranslation $translation a translation.
     */
    public function removeTranslation(GroupTranslation $translation): void
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Adds a user.
     * @param \App\Entity\User $user a user.
     */
    public function addUser(User $user): void
    {
        if ($this->users->contains($user) === false) {
            $this->users->add($user);
        }
    }

    /**
     * Removes a user.
     * @param \App\Entity\User $user a user.
     */
    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);
    }

    /**
     * Adds a role.
     * @param \App\Entity\Role $role a role.
     */
    public function addRole(Role $role): void
    {
        if ($this->roles->contains($role) === false) {
            $this->roles->add($role);
        }
    }

    /**
     * Removes a role.
     * @param \App\Entity\Role $role a role.
     */
    public function removeRole(Role $role): void
    {
        $this->roles->removeElement($role);
    }
}
