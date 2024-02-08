<?php

namespace App\Entity\Platform;

use App\Repository\Platform\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // We can use constants for roles to find usages all over the application rather
    // than doing a full-text search on the "ROLE_" string.
    // It also prevents from making typo errors.
    final public const ROLE_USER = 'ROLE_USER';
    final public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'user')]
    private Collection $services;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $language = null;

    #[ORM\Column(length: 8)]
    private int $defaultInstance = 0;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $position = null;
    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::JSON, options: ["default" => self::ROLE_USER])]
    private array $roles = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $lastLogin;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function getDefaultInstance(): int
    {
        return $this->defaultInstance;
    }

    public function setDefaultInstance(int $defaultInstance): void
    {
        $this->defaultInstance = $defaultInstance;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = self::ROLE_USER;
        }

        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getLastLogin(): \DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }
}
