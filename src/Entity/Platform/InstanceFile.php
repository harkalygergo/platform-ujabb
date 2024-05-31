<?php

namespace App\Entity\Platform;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class InstanceFile
{
    // create ID as integer, auto increment, primary key
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // create instanceId as point to Instance entity many-to-one relation
    #[ORM\ManyToOne(targetEntity: Instance::class)]
    #[ORM\JoinColumn(name: 'instance_id')]
    private ?Instance $instance = null;

    // create userId as point to User entity, many-to-one relation
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'storages')]
    private ?User $user = null;

    // create string for original uploaded file name
    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    // create string for file path
    #[ORM\Column(length: 255)]
    private ?string $path = null;

    // create string for file type
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    // create integer for file size as bytes
    #[ORM\Column(type: 'integer')]
    private ?int $size = null;

    // create boolean for publicity
    #[ORM\Column(type: 'boolean')]
    private ?bool $public = false;

    // create datetime for file creation date
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    // create datetime for file update date
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getInstance(): ?Instance
    {
        return $this->instance;
    }

    public function setInstance(?Instance $instance): void
    {
        $this->instance = $instance;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(?bool $public): void
    {
        $this->public = $public;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
