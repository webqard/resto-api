<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\User\PasswordUpgraderRepository;
use CyrilVerloop\DoctrineEntities\Active;
use CyrilVerloop\DoctrineEntities\IntId;
use CyrilVerloop\DoctrineEntities\TimestampableImmutable\ConnectedAt;
use CyrilVerloop\DoctrineEntities\TimestampableImmutable\CreatedAt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The user entity.
 */
#[ORM\Entity(repositoryClass: PasswordUpgraderRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Traits :
    use IntId;
    use Active;
    use CreatedAt;
    use ConnectedAt;


    // Properties :

    /**
     * @var string the login.
     */
    #[
        ORM\Column(type: Types::TEXT, length: 180, unique: true)
    ]
    private string $login;

    /**
     * @var string the hashed password.
     */
    #[ORM\Column(type: Types::TEXT, length: 100)]
    private string $password;

    /**
     * @var string[] the roles.
     */
    #[ORM\Column]
    private array $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection the groups.
     */
    #[
        ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'users')
    ]
    private Collection $groups;


    // Magic methods :

    /**
     * The constructor.
     * @param string $login the login.
     * @param string $password the password.
     * @param \DateTimeImmutable $createdAt the date and time of the creation.
     * @param \DateTimeImmutable|null $connectedAt the date and time of the connection
     * @param string[] $roles the roles.
     * @param bool $active the active state.
     */
    public function __construct(
        string $login,
        string $password,
        \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        ?\DateTimeImmutable $connectedAt = null,
        array $roles = [],
        bool $active = true
    ) {
        $this->id = null;
        $this->login = $login;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->connectedAt = $connectedAt;
        $this->roles = $roles;
        $this->active = $active;
        $this->groups = new ArrayCollection();
    }


    // Accessors :

    /**
     * Returns the login.
     * @return string the login.
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string the password.
     * @see \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Returns the groups.
     * @return \Doctrine\Common\Collections\Collection the groups.
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }


    // Mutators :

    /**
     * Changes the login.
     * @param string $login the login.
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * Changes the password.
     * @param string $password the password.
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    // Collections :

    /**
     * Adds a group.
     * @param \App\Entity\Group $group a group.
     */
    public function addGroup(Group $group): void
    {
        if ($this->groups->contains($group) === false) {
            $this->groups->add($group);
        }
    }

    /**
     * Removes a group.
     * @param \App\Entity\Group $group a group.
     */
    public function removeGroup(Group $group): void
    {
        $this->groups->removeElement($group);
    }


    // UserInterface :

    /**
     * @see \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles the roles.
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @see \Symfony\Component\Security\Core\User\UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->password = '';
    }

    /**
     * @return string the user identifier.
     * @see \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->login;
    }
}
