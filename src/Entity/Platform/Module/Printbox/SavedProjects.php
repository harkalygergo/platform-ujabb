<?php

namespace App\Entity\Platform\Module\Printbox;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'module_printbox_saved_project')]
#[ORM\Entity]
class SavedProjects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // add site as string
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $site = null;

    // add customer as integer
    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?int $customer = null;

    // add projectHash as string
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $projectHash = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $projectTitle = null;

    // add product as big int
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['unsigned' => true])]
    private ?int $product = null;

    // add variant as unsigned big int
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['unsigned' => true])]
    private ?int $variant = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $productTitle = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $productCategory = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): void
    {
        $this->site = $site;
    }

    public function getCustomer(): ?int
    {
        return $this->customer;
    }

    public function setCustomer(?int $customer): void
    {
        $this->customer = $customer;
    }

    public function getProjectHash(): ?string
    {
        return $this->projectHash;
    }

    public function setProjectHash(?string $projectHash): void
    {
        $this->projectHash = $projectHash;
    }

    public function getProjectTitle(): ?string
    {
        return $this->projectTitle;
    }

    public function setProjectTitle(?string $projectTitle): void
    {
        $this->projectTitle = $projectTitle;
    }

    public function getProduct(): ?int
    {
        return $this->product;
    }

    public function setProduct(?int $product): void
    {
        $this->product = $product;
    }

    public function getVariant(): ?int
    {
        return $this->variant;
    }

    public function setVariant(?int $variant): void
    {
        $this->variant = $variant;
    }

    public function getProductTitle(): ?string
    {
        return $this->productTitle;
    }

    public function setProductTitle(?string $productTitle): void
    {
        $this->productTitle = $productTitle;
    }

    public function getProductCategory(): ?string
    {
        return $this->productCategory;
    }

    public function setProductCategory(?string $productCategory): void
    {
        $this->productCategory = $productCategory;
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
}
