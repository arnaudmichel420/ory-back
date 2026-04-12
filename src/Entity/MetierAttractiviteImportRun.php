<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\MetierAttractiviteImportRunRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierAttractiviteImportRunRepository::class)]
class MetierAttractiviteImportRun
{
    use TimestampableTrait;

    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_COMPLETED_WITH_ERRORS = 'completed_with_errors';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private int $totalPairs = 0;

    #[ORM\Column]
    private int $processedPairs = 0;

    #[ORM\Column]
    private int $errorPairs = 0;

    #[ORM\Column]
    private int $ignoredValues = 0;

    #[ORM\Column]
    private int $createdValues = 0;

    #[ORM\Column]
    private int $updatedValues = 0;

    #[ORM\Column]
    private int $deletedValues = 0;

    #[ORM\Column]
    private int $totalBatches = 0;

    #[ORM\Column]
    private int $processedBatches = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalPairs(): int
    {
        return $this->totalPairs;
    }

    public function setTotalPairs(int $totalPairs): static
    {
        $this->totalPairs = max(0, $totalPairs);

        return $this;
    }

    public function getProcessedPairs(): int
    {
        return $this->processedPairs;
    }

    public function incrementProcessedPairs(int $count = 1): static
    {
        $this->processedPairs += max(0, $count);

        return $this;
    }

    public function getErrorPairs(): int
    {
        return $this->errorPairs;
    }

    public function incrementErrorPairs(int $count = 1): static
    {
        $this->errorPairs += max(0, $count);

        return $this;
    }

    public function getIgnoredValues(): int
    {
        return $this->ignoredValues;
    }

    public function incrementIgnoredValues(int $count = 1): static
    {
        $this->ignoredValues += max(0, $count);

        return $this;
    }

    public function getCreatedValues(): int
    {
        return $this->createdValues;
    }

    public function incrementCreatedValues(int $count = 1): static
    {
        $this->createdValues += max(0, $count);

        return $this;
    }

    public function getUpdatedValues(): int
    {
        return $this->updatedValues;
    }

    public function incrementUpdatedValues(int $count = 1): static
    {
        $this->updatedValues += max(0, $count);

        return $this;
    }

    public function getDeletedValues(): int
    {
        return $this->deletedValues;
    }

    public function incrementDeletedValues(int $count = 1): static
    {
        $this->deletedValues += max(0, $count);

        return $this;
    }

    public function getTotalBatches(): int
    {
        return $this->totalBatches;
    }

    public function setTotalBatches(int $totalBatches): static
    {
        $this->totalBatches = max(0, $totalBatches);

        return $this;
    }

    public function getProcessedBatches(): int
    {
        return $this->processedBatches;
    }

    public function incrementProcessedBatches(int $count = 1): static
    {
        $this->processedBatches += max(0, $count);

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function finalize(): static
    {
        $this->status = $this->errorPairs > 0
            ? self::STATUS_COMPLETED_WITH_ERRORS
            : self::STATUS_COMPLETED;
        $this->completedAt = new \DateTimeImmutable();

        return $this;
    }
}
