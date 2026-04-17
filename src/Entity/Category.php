<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['category:read']],
    denormalizationContext: ['groups' => ['category:write']],
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['category:read', 'tool:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Groups(['category:read', 'category:write', 'tool:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['category:read', 'category:write'])]
    private ?string $description = null;

    #[ORM\Column(name: 'color_hex', type: 'string', length: 7, options: ['default' => '#6366f1'])]
    #[Groups(['category:read', 'category:write'])]
    #[Assert\Regex(pattern: '/^#[0-9a-fA-F]{6}$/')]
    private string $colorHex = '#6366f1';

    #[ORM\Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['category:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Tool::class)]
    #[Groups(['category:read'])]
    private Collection $tools;

    public function __construct()
    {
        $this->tools = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getColorHex(): string { return $this->colorHex; }
    public function setColorHex(string $colorHex): static { $this->colorHex = $colorHex; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getTools(): Collection { return $this->tools; }
}
