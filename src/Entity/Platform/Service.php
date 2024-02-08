<?php

namespace App\Entity\Platform;

use App\Repository\Platform\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'services')]
    private ?User $user = null;

    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nextPayment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getNextPayment(): ?\DateTimeInterface
    {
        return $this->nextPayment;
    }

    public function setNextPayment(?\DateTimeInterface $nextPayment): static
    {
        $this->nextPayment = $nextPayment;

        return $this;
    }
}
