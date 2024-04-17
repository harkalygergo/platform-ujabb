<?php

namespace App\Entity\Platform;

use App\Repository\Platform\WebsiteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebsiteRepository::class)]
class Website
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $domain = null;

    #[ORM\Column(length: 128)]
    private ?int $instance = null;

    #[ORM\Column(length: 8)]
    private int $status = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keywords = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 32)]
    private ?string $robots = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $language = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $template = null;

    #[ORM\Column(nullable: true)]
    private ?int $logo = null;

    #[ORM\Column(nullable: true)]
    private ?int $favicon = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $primaryColor = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $secondaryColor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $htmlHead = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $htmlBody = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $htmlFooter = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstance(): ?int
    {
        return $this->instance;
    }

    public function setInstance(?int $instance): void
    {
        $this->instance = $instance;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
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

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): static
    {
        $this->slogan = $slogan;

        return $this;
    }

    public function getRobots(): ?string
    {
        return $this->robots;
    }

    public function setRobots(string $robots): static
    {
        $this->robots = $robots;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getLogo(): ?int
    {
        return $this->logo;
    }

    public function setLogo(?int $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getFavicon(): ?int
    {
        return $this->favicon;
    }

    public function setFavicon(?int $favicon): static
    {
        $this->favicon = $favicon;

        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    public function setPrimaryColor(?string $primaryColor): static
    {
        $this->primaryColor = $primaryColor;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondaryColor;
    }

    public function setSecondaryColor(?string $secondaryColor): static
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }

    public function getHtmlHead(): ?string
    {
        return $this->htmlHead;
    }

    public function setHtmlHead(?string $htmlHead): static
    {
        $this->htmlHead = $htmlHead;

        return $this;
    }

    public function getHtmlBody(): ?string
    {
        return $this->htmlBody;
    }

    public function setHtmlBody(?string $htmlBody): static
    {
        $this->htmlBody = $htmlBody;

        return $this;
    }

    public function getHtmlFooter(): ?string
    {
        return $this->htmlFooter;
    }

    public function setHtmlFooter(?string $htmlFooter): static
    {
        $this->htmlFooter = $htmlFooter;

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
}
