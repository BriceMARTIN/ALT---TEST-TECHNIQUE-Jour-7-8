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
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\UsageLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsageLogRepository::class)]
#[ORM\Table(name: 'usage_logs')]
#[ORM\Index(name: 'idx_usage_date_tool', columns: ['session_date', 'tool_id'])]
#[ORM\Index(name: 'idx_usage_user_date', columns: ['user_id', 'session_date'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['usage_log:read']],
    denormalizationContext: ['groups' => ['usage_log:write']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'user' => 'exact',
    'tool' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['sessionDate'])]
#[ApiFilter(RangeFilter::class, properties: ['usageMinutes', 'actionsCount'])]
#[ApiFilter(OrderFilter::class, properties: ['sessionDate', 'usageMinutes', 'actionsCount'])]
class UsageLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['usage_log:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'usageLogs')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['usage_log:read', 'usage_log:write'])]
    #[Assert\NotNull]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Tool::class, inversedBy: 'usageLogs')]
    #[ORM\JoinColumn(name: 'tool_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['usage_log:read', 'usage_log:write'])]
    #[Assert\NotNull]
    private Tool $tool;

    #[ORM\Column(name: 'session_date', type: 'date')]
    #[Groups(['usage_log:read', 'usage_log:write'])]
    #[Assert\NotNull]
    private \DateTimeInterface $sessionDate;

    #[ORM\Column(name: 'usage_minutes', type: 'integer', options: ['default' => 0])]
    #[Groups(['usage_log:read', 'usage_log:write'])]
    #[Assert\PositiveOrZero]
    private int $usageMinutes = 0;

    #[ORM\Column(name: 'actions_count', type: 'integer', options: ['default' => 0])]
    #[Groups(['usage_log:read', 'usage_log:write'])]
    #[Assert\PositiveOrZero]
    private int $actionsCount = 0;

    #[ORM\Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['usage_log:read'])]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getTool(): Tool { return $this->tool; }
    public function setTool(Tool $tool): static { $this->tool = $tool; return $this; }

    public function getSessionDate(): \DateTimeInterface { return $this->sessionDate; }
    public function setSessionDate(\DateTimeInterface $sessionDate): static { $this->sessionDate = $sessionDate; return $this; }

    public function getUsageMinutes(): int { return $this->usageMinutes; }
    public function setUsageMinutes(int $usageMinutes): static { $this->usageMinutes = $usageMinutes; return $this; }

    public function getActionsCount(): int { return $this->actionsCount; }
    public function setActionsCount(int $actionsCount): static { $this->actionsCount = $actionsCount; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
}
