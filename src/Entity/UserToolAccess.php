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
use App\Repository\UserToolAccessRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserToolAccessRepository::class)]
#[ORM\Table(name: 'user_tool_access')]
#[ORM\UniqueConstraint(name: 'unique_user_tool_active', columns: ['user_id', 'tool_id', 'status'])]
#[ORM\Index(name: 'idx_access_user', columns: ['user_id'])]
#[ORM\Index(name: 'idx_access_tool', columns: ['tool_id'])]
#[ORM\Index(name: 'idx_access_granted_date', columns: ['granted_at'])]
#[ORM\Index(name: 'idx_access_status', columns: ['status'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['user_tool_access:read']],
    denormalizationContext: ['groups' => ['user_tool_access:write']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'user' => 'exact',
    'tool' => 'exact',
    'status' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['grantedAt', 'revokedAt'])]
class UserToolAccess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user_tool_access:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'toolAccesses')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['user_tool_access:read', 'user_tool_access:write'])]
    #[Assert\NotNull]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Tool::class, inversedBy: 'userToolAccesses')]
    #[ORM\JoinColumn(name: 'tool_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['user_tool_access:read', 'user_tool_access:write'])]
    #[Assert\NotNull]
    private Tool $tool;

    #[ORM\Column(name: 'granted_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user_tool_access:read'])]
    private \DateTimeInterface $grantedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'grantedAccesses')]
    #[ORM\JoinColumn(name: 'granted_by', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    #[Groups(['user_tool_access:read', 'user_tool_access:write'])]
    #[Assert\NotNull]
    private User $grantedBy;

    #[ORM\Column(name: 'revoked_at', type: 'datetime', nullable: true)]
    #[Groups(['user_tool_access:read', 'user_tool_access:write'])]
    private ?\DateTimeInterface $revokedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'revokedAccesses')]
    #[ORM\JoinColumn(name: 'revoked_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['user_tool_access:read', 'user_tool_access:write'])]
    private ?User $revokedBy = null;

    #[ORM\Column(type: 'string', length: 10, columnDefinition: "ENUM('active','revoked') DEFAULT 'active'")]
    #[Groups(['user_tool_access:read', 'user_tool_access:write'])]
    #[Assert\Choice(choices: ['active', 'revoked'])]
    private string $status = 'active';

    public function __construct()
    {
        $this->grantedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getTool(): Tool { return $this->tool; }
    public function setTool(Tool $tool): static { $this->tool = $tool; return $this; }

    public function getGrantedAt(): \DateTimeInterface { return $this->grantedAt; }
    public function setGrantedAt(\DateTimeInterface $grantedAt): static { $this->grantedAt = $grantedAt; return $this; }

    public function getGrantedBy(): User { return $this->grantedBy; }
    public function setGrantedBy(User $grantedBy): static { $this->grantedBy = $grantedBy; return $this; }

    public function getRevokedAt(): ?\DateTimeInterface { return $this->revokedAt; }
    public function setRevokedAt(?\DateTimeInterface $revokedAt): static { $this->revokedAt = $revokedAt; return $this; }

    public function getRevokedBy(): ?User { return $this->revokedBy; }
    public function setRevokedBy(?User $revokedBy): static { $this->revokedBy = $revokedBy; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
}
