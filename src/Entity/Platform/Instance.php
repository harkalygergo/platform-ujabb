<?php

namespace App\Entity\Platform;

use App\Repository\Platform\InstanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstanceRepository::class)]
class Instance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $owner = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intranet = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $updatedAt;

    #[ORM\Column(length: 8)]
    private int $status = 0;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'instances')]
    #[ORM\JoinTable(name: 'user_instance')]
    private $users;

    // instanceFile one-to-many relation
    #[ORM\OneToMany(targetEntity: InstanceFile::class, mappedBy: 'instance')]
    private Collection $files;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->users = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getIntranet(): ?string
    {
        return $this->intranet;
    }

    public function setIntranet(?string $intranet): static
    {
        $this->intranet = $intranet;

        return $this;
    }

    public function getOwner(): ?int
    {
        return $this->owner;
    }

    public function setOwner(?int $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function addUser(User $param)
    {
        $this->users[] = $param;

        return $this;
    }

    public function removeUser(User $param)
    {
        $this->users->removeElement($param);

        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }
}
