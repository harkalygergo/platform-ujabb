<?php

namespace App\Entity\Platform\Module\Shopify;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'module_shopify_ecard')]
#[ORM\Entity]
class ECard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $orderJSON = null;

    // add userId as integer
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $userId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $projects = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderJSON(): ?string
    {
        return $this->orderJSON;
    }

    public function setOrderJSON(?string $orderJSON): void
    {
        $this->orderJSON = $orderJSON;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getProjects(): ?string
    {
        return $this->projects;
    }

    public function setProjects(?string $projects): void
    {
        $this->projects = $projects;
    }
}
