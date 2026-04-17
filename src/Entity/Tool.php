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
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\ToolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ToolRepository::class)]
#[ORM\Table(name: 'tools')]
#[ORM\Index(name: 'idx_tools_category', columns: ['category_id'])]
#[ORM\Index(name: 'idx_tools_department', columns: ['owner_department'])]
#[ORM\Index(name: 'idx_tools_status', columns: ['status'])]
#[ApiResource(
    operations: [
        new GetCollection(
            // API Platform automatically handles filtering and pagination based on defined filters on RangeFilter and OrderFilter
            // uriTemplate: '/tools{?name,vendor,status,ownerDepartment,category,monthlyCost[gte],monthlyCost[lte],activeUsersCount[gte],activeUsersCount[lte],order}',
            normalizationContext: ['groups' => ['tool:read']]
        ),
        new Get(
            // /tools/{id} is already the default behavior for Get operations
            // uriTemplate: '/tools/{id}',
            normalizationContext: ['groups' => ['tool:read']]
        ),
        new Post(
            // uriTemplate: '/tools',
            denormalizationContext: ['groups' => ['tool:write']],
            controller: App\Controller\CreateToolAction::class
        ),
        new Put(
            // uriTemplate: '/tools/{id}',
            denormalizationContext: ['groups' => ['tool:write']],
            controller: App\Controller\UpdateToolAction::class
        ),
        // new Delete(),
    ],
    normalizationContext: ['groups' => ['tool:read']],
    denormalizationContext: ['groups' => ['tool:write']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'vendor' => 'partial',
    'status' => 'exact',
    'ownerDepartment' => 'exact',
    'category' => 'exact',
])]
#[ApiFilter(RangeFilter::class, properties: ['monthlyCost', 'activeUsersCount'])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'monthlyCost', 'activeUsersCount', 'createdAt'])]
class Tool
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['tool:read', 'user_tool_access:read', 'access_request:read', 'usage_log:read', 'cost_tracking:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['tool:read', 'tool:write', 'user_tool_access:read', 'access_request:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['tool:read', 'tool:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['tool:read', 'tool:write'])]
    private ?string $vendor = null;

    #[ORM\Column(name: 'website_url', type: 'string', length: 255, nullable: true)]
    #[Groups(['tool:read', 'tool:write'])]
    #[Assert\Url]
    private ?string $websiteUrl = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'tools')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    #[Groups(['tool:read', 'tool:write'])]
    #[Assert\NotNull]
    private Category $category;

    #[ORM\Column(name: 'monthly_cost', type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['tool:read', 'tool:write'])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private string $monthlyCost;

    #[ORM\Column(name: 'active_users_count', type: 'integer', options: ['default' => 0])]
    #[Groups(['tool:read', 'tool:write'])]
    #[Assert\PositiveOrZero]
    private int $activeUsersCount = 0;

    #[ORM\Column(name: 'owner_department', type: 'string', length: 20, columnDefinition: "ENUM('Engineering','Sales','Marketing','HR','Finance','Operations','Design') NOT NULL")]
    #[Groups(['tool:read', 'tool:write'])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['Engineering', 'Sales', 'Marketing', 'HR', 'Finance', 'Operations', 'Design'])]
    private string $ownerDepartment;

    #[ORM\Column(type: 'string', length: 10, columnDefinition: "ENUM('active','deprecated','trial') DEFAULT 'active'")]
    #[Groups(['tool:read', 'tool:write'])]
    #[Assert\Choice(choices: ['active', 'deprecated', 'trial'])]
    private string $status = 'active';

    #[ORM\Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['tool:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['tool:read'])]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToMany(mappedBy: 'tool', targetEntity: UserToolAccess::class)]
    private Collection $userToolAccesses;

    #[ORM\OneToMany(mappedBy: 'tool', targetEntity: AccessRequest::class)]
    private Collection $accessRequests;

    #[ORM\OneToMany(mappedBy: 'tool', targetEntity: UsageLog::class)]
    private Collection $usageLogs;

    #[ORM\OneToMany(mappedBy: 'tool', targetEntity: CostTracking::class)]
    private Collection $costTrackings;

    public function __construct()
    {
        $this->userToolAccesses = new ArrayCollection();
        $this->accessRequests = new ArrayCollection();
        $this->usageLogs = new ArrayCollection();
        $this->costTrackings = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getVendor(): ?string { return $this->vendor; }
    public function setVendor(?string $vendor): static { $this->vendor = $vendor; return $this; }

    public function getWebsiteUrl(): ?string { return $this->websiteUrl; }
    public function setWebsiteUrl(?string $websiteUrl): static { $this->websiteUrl = $websiteUrl; return $this; }

    public function getCategory(): Category { return $this->category; }
    public function setCategory(Category $category): static { $this->category = $category; return $this; }

    public function getMonthlyCost(): string { return $this->monthlyCost; }
    public function setMonthlyCost(string $monthlyCost): static { $this->monthlyCost = $monthlyCost; return $this; }

    public function getActiveUsersCount(): int { return $this->activeUsersCount; }
    public function setActiveUsersCount(int $activeUsersCount): static { $this->activeUsersCount = $activeUsersCount; return $this; }

    public function getOwnerDepartment(): string { return $this->ownerDepartment; }
    public function setOwnerDepartment(string $ownerDepartment): static { $this->ownerDepartment = $ownerDepartment; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getUserToolAccesses(): Collection { return $this->userToolAccesses; }
    public function getAccessRequests(): Collection { return $this->accessRequests; }
    public function getUsageLogs(): Collection { return $this->usageLogs; }
    public function getCostTrackings(): Collection { return $this->costTrackings; }
}
