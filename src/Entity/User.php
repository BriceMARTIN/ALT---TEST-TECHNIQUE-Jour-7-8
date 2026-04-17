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
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\Index(name: 'idx_users_department', columns: ['department'])]
#[ORM\Index(name: 'idx_users_status', columns: ['status'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'email' => 'partial',
    'department' => 'exact',
    'role' => 'exact',
    'status' => 'exact',
])]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read', 'user_tool_access:read', 'access_request:read', 'usage_log:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['user:read', 'user:write', 'user_tool_access:read', 'access_request:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: 'string', length: 20, columnDefinition: "ENUM('Engineering','Sales','Marketing','HR','Finance','Operations','Design') NOT NULL")]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Choice(choices: ['Engineering', 'Sales', 'Marketing', 'HR', 'Finance', 'Operations', 'Design'])]
    private string $department;

    #[ORM\Column(type: 'string', length: 10, columnDefinition: "ENUM('employee','manager','admin') DEFAULT 'employee'")]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Choice(choices: ['employee', 'manager', 'admin'])]
    private string $role = 'employee';

    #[ORM\Column(type: 'string', length: 10, columnDefinition: "ENUM('active','inactive') DEFAULT 'active'")]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    private string $status = 'active';

    #[ORM\Column(name: 'hire_date', type: 'date', nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:read'])]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserToolAccess::class)]
    private Collection $toolAccesses;

    #[ORM\OneToMany(mappedBy: 'grantedBy', targetEntity: UserToolAccess::class)]
    private Collection $grantedAccesses;

    #[ORM\OneToMany(mappedBy: 'revokedBy', targetEntity: UserToolAccess::class)]
    private Collection $revokedAccesses;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AccessRequest::class)]
    private Collection $accessRequests;

    #[ORM\OneToMany(mappedBy: 'processedBy', targetEntity: AccessRequest::class)]
    private Collection $processedRequests;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UsageLog::class)]
    private Collection $usageLogs;

    public function __construct()
    {
        $this->toolAccesses = new ArrayCollection();
        $this->grantedAccesses = new ArrayCollection();
        $this->revokedAccesses = new ArrayCollection();
        $this->accessRequests = new ArrayCollection();
        $this->processedRequests = new ArrayCollection();
        $this->usageLogs = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getDepartment(): string { return $this->department; }
    public function setDepartment(string $department): static { $this->department = $department; return $this; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): static { $this->role = $role; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getHireDate(): ?\DateTimeInterface { return $this->hireDate; }
    public function setHireDate(?\DateTimeInterface $hireDate): static { $this->hireDate = $hireDate; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getToolAccesses(): Collection { return $this->toolAccesses; }
    public function getGrantedAccesses(): Collection { return $this->grantedAccesses; }
    public function getRevokedAccesses(): Collection { return $this->revokedAccesses; }
    public function getAccessRequests(): Collection { return $this->accessRequests; }
    public function getProcessedRequests(): Collection { return $this->processedRequests; }
    public function getUsageLogs(): Collection { return $this->usageLogs; }
}
