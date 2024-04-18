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

    #[ORM\ManyToOne(targetEntity: Instance::class)]
    #[ORM\JoinColumn(name: "default_instance_id")]
    private ?Instance $defaultInstance;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $position = null;

    // add boolean status, default true
    #[ORM\Column(type: Types::BOOLEAN, options: ["default" => true])]
    private bool $status = true;

    /**
     * @var string[]
     */
    //#[ORM\Column(type: Types::JSON, options: ["default" => '["'.self::ROLE_USER.'"]'])]
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    // last login defaults to 1970-01-01 00:00:00
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "1970-01-01 00:00:00"])]
    private \DateTime $lastLogin;

    #[ORM\ManyToMany(targetEntity: BillingProfile::class, mappedBy: 'User')]
    private Collection $billingProfiles;

    // add profile image url, default null
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImageUrl = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->billingProfiles = new ArrayCollection();
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

    public function getDefaultInstance(): ?Instance
    {
        return $this->defaultInstance;
    }

    public function setDefaultInstance(?Instance $defaultInstance): void
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

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
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

    /**
     * @return Collection<int, BillingProfile>
     */
    public function getBillingProfiles(): Collection
    {
        return $this->billingProfiles;
    }

    public function addBillingProfile(BillingProfile $billingProfile): static
    {
        if (!$this->billingProfiles->contains($billingProfile)) {
            $this->billingProfiles->add($billingProfile);
            $billingProfile->addUser($this);
        }

        return $this;
    }

    public function removeBillingProfile(BillingProfile $billingProfile): static
    {
        if ($this->billingProfiles->removeElement($billingProfile)) {
            $billingProfile->removeUser($this);
        }

        return $this;
    }

    public function getProfileImageUrl(): ?string
    {
        return $this->profileImageUrl;
    }

    public function setProfileImageUrl(?string $profileImageUrl): static
    {
        $this->profileImageUrl = $profileImageUrl;

        return $this;
    }
}
