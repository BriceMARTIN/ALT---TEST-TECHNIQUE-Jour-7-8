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
use App\Repository\CostTrackingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CostTrackingRepository::class)]
#[ORM\Table(name: 'cost_tracking')]
#[ORM\UniqueConstraint(name: 'unique_tool_month', columns: ['tool_id', 'month_year'])]
#[ORM\Index(name: 'idx_cost_month_tool', columns: ['month_year', 'tool_id'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['cost_tracking:read']],
    denormalizationContext: ['groups' => ['cost_tracking:write']],
)]
#[ApiFilter(SearchFilter::class, properties: ['tool' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['monthYear'])]
#[ApiFilter(RangeFilter::class, properties: ['totalMonthlyCost', 'activeUsersCount'])]
#[ApiFilter(OrderFilter::class, properties: ['monthYear', 'totalMonthlyCost', 'activeUsersCount'])]
class CostTracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['cost_tracking:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tool::class, inversedBy: 'costTrackings')]
    #[ORM\JoinColumn(name: 'tool_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['cost_tracking:read', 'cost_tracking:write'])]
    #[Assert\NotNull]
    private Tool $tool;

    // Stored as DATE in MySQL (day always 01, represents month+year)
    #[ORM\Column(name: 'month_year', type: 'date')]
    #[Groups(['cost_tracking:read', 'cost_tracking:write'])]
    #[Assert\NotNull]
    private \DateTimeInterface $monthYear;

    #[ORM\Column(name: 'total_monthly_cost', type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['cost_tracking:read', 'cost_tracking:write'])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private string $totalMonthlyCost;

    #[ORM\Column(name: 'active_users_count', type: 'integer', options: ['default' => 0])]
    #[Groups(['cost_tracking:read', 'cost_tracking:write'])]
    #[Assert\PositiveOrZero]
    private int $activeUsersCount = 0;

    #[ORM\Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['cost_tracking:read'])]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getTool(): Tool { return $this->tool; }
    public function setTool(Tool $tool): static { $this->tool = $tool; return $this; }

    public function getMonthYear(): \DateTimeInterface { return $this->monthYear; }
    public function setMonthYear(\DateTimeInterface $monthYear): static { $this->monthYear = $monthYear; return $this; }

    public function getTotalMonthlyCost(): string { return $this->totalMonthlyCost; }
    public function setTotalMonthlyCost(string $totalMonthlyCost): static { $this->totalMonthlyCost = $totalMonthlyCost; return $this; }

    public function getActiveUsersCount(): int { return $this->activeUsersCount; }
    public function setActiveUsersCount(int $activeUsersCount): static { $this->activeUsersCount = $activeUsersCount; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
}
