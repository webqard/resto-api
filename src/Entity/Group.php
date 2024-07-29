<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\User;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\Slug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
    use Slug;


    // Properties :

    /**
     * @var string the name.
     */
    #[ORM\Column(type: Types::TEXT, unique: true)]
    private string $name;

    /**
     * @var string the slug.
     */
//    #[ORM\Column(type: Types::TEXT, unique: true)]
//    private string $slug;

    /**
     * @var string|null the description.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

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
     * @param string $name the name.
     * @param string|null $slug the slug.
     * @param string|null $description the description.
     */
    public function __construct(
        string $name,
        ?string $slug,
        ?string $description
    ) {
        $this->id = null;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }


    // Accessors :

    /**
     * Returns the name.
     * @return string the name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the description.
     * @return string|null the description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
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


    // Mutators :

    /**
     * Changes the name.
     * @param string $name the name.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Changes the description.
     * @param string|null $description the description.
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


    // Collections :

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
