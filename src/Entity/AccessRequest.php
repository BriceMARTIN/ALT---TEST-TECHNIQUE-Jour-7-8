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
use App\Repository\AccessRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccessRequestRepository::class)]
#[ORM\Table(name: 'access_requests')]
#[ORM\Index(name: 'idx_requests_status', columns: ['status'])]
#[ORM\Index(name: 'idx_requests_user', columns: ['user_id'])]
#[ORM\Index(name: 'idx_requests_date', columns: ['requested_at'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['access_request:read']],
    denormalizationContext: ['groups' => ['access_request:write']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'user' => 'exact',
    'tool' => 'exact',
    'status' => 'exact',
    'processedBy' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['requestedAt', 'processedAt'])]
class AccessRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['access_request:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'accessRequests')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['access_request:read', 'access_request:write'])]
    #[Assert\NotNull]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Tool::class, inversedBy: 'accessRequests')]
    #[ORM\JoinColumn(name: 'tool_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['access_request:read', 'access_request:write'])]
    #[Assert\NotNull]
    private Tool $tool;

    #[ORM\Column(name: 'business_justification', type: 'text')]
    #[Groups(['access_request:read', 'access_request:write'])]
    #[Assert\NotBlank]
    private string $businessJustification;

    #[ORM\Column(type: 'string', length: 10, columnDefinition: "ENUM('pending','approved','rejected') DEFAULT 'pending'")]
    #[Groups(['access_request:read', 'access_request:write'])]
    #[Assert\Choice(choices: ['pending', 'approved', 'rejected'])]
    private string $status = 'pending';

    #[ORM\Column(name: 'requested_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['access_request:read'])]
    private \DateTimeInterface $requestedAt;

    #[ORM\Column(name: 'processed_at', type: 'datetime', nullable: true)]
    #[Groups(['access_request:read', 'access_request:write'])]
    private ?\DateTimeInterface $processedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'processedRequests')]
    #[ORM\JoinColumn(name: 'processed_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['access_request:read', 'access_request:write'])]
    private ?User $processedBy = null;

    #[ORM\Column(name: 'processing_notes', type: 'text', nullable: true)]
    #[Groups(['access_request:read', 'access_request:write'])]
    private ?string $processingNotes = null;

    public function __construct()
    {
        $this->requestedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getTool(): Tool { return $this->tool; }
    public function setTool(Tool $tool): static { $this->tool = $tool; return $this; }

    public function getBusinessJustification(): string { return $this->businessJustification; }
    public function setBusinessJustification(string $businessJustification): static { $this->businessJustification = $businessJustification; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getRequestedAt(): \DateTimeInterface { return $this->requestedAt; }
    public function setRequestedAt(\DateTimeInterface $requestedAt): static { $this->requestedAt = $requestedAt; return $this; }

    public function getProcessedAt(): ?\DateTimeInterface { return $this->processedAt; }
    public function setProcessedAt(?\DateTimeInterface $processedAt): static { $this->processedAt = $processedAt; return $this; }

    public function getProcessedBy(): ?User { return $this->processedBy; }
    public function setProcessedBy(?User $processedBy): static { $this->processedBy = $processedBy; return $this; }

    public function getProcessingNotes(): ?string { return $this->processingNotes; }
    public function setProcessingNotes(?string $processingNotes): static { $this->processingNotes = $processingNotes; return $this; }
}
